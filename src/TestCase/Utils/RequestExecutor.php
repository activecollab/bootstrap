<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\TestCase\Utils;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\Session\RepositoryInterface as SessionRepositoryInterface;
use ActiveCollab\Authentication\Session\SessionInterface;
use ActiveCollab\Authentication\Token\RepositoryInterface as TokenRepositoryInterface;
use ActiveCollab\Authentication\Token\TokenInterface;
use ActiveCollab\Bootstrap\AppBootstrapper\AppBootstrapperInterface;
use ActiveCollab\Bootstrap\AppBootstrapper\Web\WebAppBootstrapperInterface;
use ActiveCollab\Cookies\CookiesInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Slim\Http\Environment as SlimEnvironment;
use Slim\Http\Request as SlimRequest;
use Slim\Http\Response as SlimResponse;

/**
 * @package ActiveCollab\Bootstrap\TestCase\Utils
 */
class RequestExecutor implements RequestExecutorInterface
{
    /**
     * @var AppBootstrapperInterface|WebAppBootstrapperInterface
     */
    private $app_bootstrapper;

    /**
     * @var SessionRepositoryInterface
     */
    private $session_repository;

    /**
     * @var TokenRepositoryInterface
     */
    private $token_repository;

    /**
     * @var CookiesInterface
     */
    private $cookies;

    /**
     * @var string
     */
    private $session_id_cookie_name;

    /**
     * @var AuthenticatedUserInterface
     */
    private $impersonate_user;

    /**
     * @var string
     */
    private $authentication_method = self::SESSION;

    /**
     * RequestExecutor constructor.
     *
     * @param AppBootstrapperInterface   $app_bootstrapper
     * @param SessionRepositoryInterface $session_repository
     * @param TokenRepositoryInterface   $token_repository
     * @param CookiesInterface           $cookies
     * @param string                     $session_id_cookie_name
     */
    public function __construct(
        AppBootstrapperInterface $app_bootstrapper,
        SessionRepositoryInterface $session_repository,
        TokenRepositoryInterface $token_repository,
        CookiesInterface $cookies,
        string $session_id_cookie_name = 'sessid'
    ) {
        $this->app_bootstrapper = $app_bootstrapper;
        $this->session_repository = $session_repository;
        $this->token_repository = $token_repository;
        $this->cookies = $cookies;
        $this->session_id_cookie_name = $session_id_cookie_name;

        if (!$this->app_bootstrapper->isBootstrapped()) {
            $this->app_bootstrapper->bootstrap();
        }
    }

    public function &as(AuthenticatedUserInterface $user, string $authentication_method = self::SESSION): RequestExecutorInterface
    {
        $this->impersonate_user = $user;
        $this->authentication_method = $authentication_method;

        return $this;
    }

    private $using_session;

    public function &usingSession(SessionInterface $session): RequestExecutorInterface
    {
        $this->using_session = $session;

        return $this;
    }

    private $using_token;

    public function &usingToken(TokenInterface $token): RequestExecutorInterface
    {
        $this->using_token = $token;

        return $this;
    }

    public function get(string $path, array $query_params = [], callable $modify_request_and_response = null): ResponseInterface
    {
        return $this->executeRequest($this->createRequest('GET', $path, $query_params), null, $modify_request_and_response);
    }

    public function post(string $path, array $payload = [], callable $modify_request_and_response = null): ResponseInterface
    {
        return $this->executeRequest($this->createRequest('POST', $path, [], $payload), null, $modify_request_and_response);
    }

    public function put(string $path, array $payload = [], callable $modify_request_and_response = null): ResponseInterface
    {
        return $this->executeRequest($this->createRequest('PUT', $path, [], $payload), null, $modify_request_and_response);
    }

    public function delete(string $path, array $payload = [], callable $modify_request_and_response = null): ResponseInterface
    {
        return $this->executeRequest($this->createRequest('DELETE', $path, [], $payload), null, $modify_request_and_response);
    }

    /**
     * Execute request and return response.
     *
     * @param  ServerRequestInterface $request
     * @param  ResponseInterface|null $response
     * @param  callable|null          $modify_request_and_response
     * @return ResponseInterface
     */
    private function executeRequest(ServerRequestInterface $request, ResponseInterface $response = null, callable $modify_request_and_response = null): ResponseInterface
    {
        if (!$response) {
            $response = $this->createResponse();
        }

        if ($this->impersonate_user) {
            list($request, $response) = $this->prepareRequestAndResponseFor($this->impersonate_user, $request, $response, $this->authentication_method === self::SESSION);

            if (!$request instanceof ServerRequestInterface || !$response instanceof ResponseInterface) {
                throw new RuntimeException('Request/response modification callback is expected to return a modified request');
            }
        }

        if (is_callable($modify_request_and_response)) {
            list($request, $response) = $modify_request_and_response($request, $response);

            if (!$request instanceof ServerRequestInterface || !$response instanceof ResponseInterface) {
                throw new RuntimeException('Request/response modification callback is expected to return a modified request');
            }
        }

        return $this->app_bootstrapper->process($request, $response);
    }

    /**
     * Prepare request and response for requests that are being made by an authenticated user.
     *
     * @param  AuthenticatedUserInterface $user
     * @param  ServerRequestInterface     $request
     * @param  ResponseInterface|null     $response
     * @param  bool                       $use_session
     * @return array
     */
    private function prepareRequestAndResponseFor(AuthenticatedUserInterface $user, ServerRequestInterface $request, ResponseInterface $response = null, $use_session = true)
    {
        if ($use_session) {
            $authenticated_with = $this->using_session ? $this->using_session : $this->createUserSession($user);
        } else {
            $authenticated_with = $this->using_token ? $this->using_token : $this->createToken($user);
        }

        if ($response === null) {
            $response = $this->createResponse();
        }

        if ($authenticated_with instanceof SessionInterface) {
            list($request, $response) = $this->cookies->set($request, $response, $this->session_id_cookie_name, $authenticated_with->getSessionId());
        } else {
            $request = $request->withHeader('Authorization', 'Bearer ' . $authenticated_with->getTokenId());
        }

        return [$request, $response];
    }

    /**
     * Create a request instance from the given arguments.
     *
     * @param  string                 $method
     * @param  string                 $path
     * @param  array                  $query_params
     * @param  array|null             $payload
     * @return ServerRequestInterface
     */
    private function createRequest(string $method, string $path, array $query_params = [], array $payload = null): ServerRequestInterface
    {
        $environment_user_data = [
            'REQUEST_METHOD' => $method,
            'REQUEST_URI' => $this->prepareRequestUri($path),
        ];

        if (!empty($query_params)) {
            $environment_user_data['QUERY_STRING'] = http_build_query($query_params);
        }

        $environment = SlimEnvironment::mock($environment_user_data);

        $request = SlimRequest::createFromEnvironment($environment)
            ->withParsedBody($payload);

        return $request;
    }

    /**
     * Prepare request URI.
     *
     * @param  string $path
     * @return string
     */
    private function prepareRequestUri(string $path)
    {
        return '/' . trim($path, '/');
    }

    /**
     * @return ResponseInterface
     */
    protected function createResponse(): ResponseInterface
    {
        return new SlimResponse();
    }

    /**
     * Create a new session for $user.
     *
     * @param  AuthenticatedUserInterface $user
     * @param  bool                       $remember
     * @return SessionInterface
     */
    private function createUserSession(AuthenticatedUserInterface $user, $remember = false): SessionInterface
    {
        return $this->session_repository->createSession($user, ['remember' => (bool) $remember]);
    }

    /**
     * Create a new token for $user.
     *
     * @param  AuthenticatedUserInterface $user
     * @return TokenInterface
     */
    private function createToken(AuthenticatedUserInterface $user): TokenInterface
    {
        return $this->token_repository->issueToken($user);
    }
}
