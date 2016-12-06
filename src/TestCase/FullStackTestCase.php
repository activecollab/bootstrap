<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\TestCase;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\Session\RepositoryInterface as SessionRepositoryInterface;
use ActiveCollab\Authentication\Session\SessionInterface;
use ActiveCollab\Authentication\Token\RepositoryInterface as TokenRepositoryInterface;
use ActiveCollab\Authentication\Token\TokenInterface;
use ActiveCollab\Bootstrap\AppBootstrapper\AppBootstrapperInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Slim\Http\Environment as SlimEnvironment;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * @property \ActiveCollab\Authentication\AuthenticationInterface $authentication
 * @property \ActiveCollab\Cookies\CookiesInterface $cookies
 * @property \ActiveCollab\Encryptor\EncryptorInterface $encryptor
 * @property string $session_id_cookie_name
 *
 * @package ActiveCollab\Bootstrap\TestCase
 */
abstract class FullStackTestCase extends ModelTestCase
{
    /**
     * @param ResponseInterface|mixed $response
     */
    protected function assertJsonResponse($response)
    {
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertContains('application/json', $response->getHeaderLine('Content-Type'));
    }

    /**
     * Execute a GET request and return resulting request and response.
     *
     * @param  string            $path
     * @param  array             $query_params
     * @param  callable|null     $modify_request_and_response
     * @return ResponseInterface
     */
    protected function executeGetRequest(string $path, array $query_params = [], callable $modify_request_and_response = null)
    {
        return $this->executeRequest($this->createRequest('GET', $path, $query_params), null, $modify_request_and_response);
    }

    /**
     * Execute GET request as a given user.
     *
     * @param  AuthenticatedUserInterface $user
     * @param  string                     $path
     * @param  array|null                 $query_params
     * @param  callable|null              $modify_request_and_response
     * @return ResponseInterface
     */
    public function executeGetRequestAs(AuthenticatedUserInterface $user, string $path, array $query_params = [], callable $modify_request_and_response = null)
    {
        $request = $this->createRequest('GET', $path, $query_params);

        /** @var ServerRequestInterface $request */
        /** @var ResponseInterface $response */
        list($request, $response) = $this->prepareRequestAndResponseFor($user, $request);

        return $this->executeRequest($request, $response, $modify_request_and_response);
    }

    /**
     * Execute POST request.
     *
     * @param  string            $path
     * @param  array|null        $payload
     * @param  callable|null     $modify_request_and_response
     * @return ResponseInterface
     */
    public function executePostRequest(string $path, array $payload = null, callable $modify_request_and_response = null)
    {
        return $this->executeRequest($this->createRequest('POST', $path, [], $payload), null, $modify_request_and_response);
    }

    /**
     * Execute POST request as $user.
     *
     * @param  AuthenticatedUserInterface $user
     * @param  string                     $path
     * @param  array|null                 $payload
     * @param  callable|null              $modify_request_and_response
     * @return ResponseInterface
     */
    public function executePostRequestAs(AuthenticatedUserInterface $user, string $path, $payload = null, callable $modify_request_and_response = null)
    {
        $request = $this->createRequest('POST', $path, [], $payload);

        /** @var ServerRequestInterface $request */
        /** @var ResponseInterface $response */
        list($request, $response) = $this->prepareRequestAndResponseFor($user, $request);

        return $this->executeRequest($request, $response, $modify_request_and_response);
    }

    /**
     * Execute POST request.
     *
     * @param  string            $path
     * @param  array|null        $payload
     * @param  callable|null     $modify_request_and_response
     * @return ResponseInterface
     */
    public function executePutRequest(string $path, array $payload = null, callable $modify_request_and_response = null)
    {
        return $this->executeRequest($this->createRequest('PUT', $path, [], $payload), null, $modify_request_and_response);
    }

    /**
     * Execute POST request as $user.
     *
     * @param  AuthenticatedUserInterface $user
     * @param  string                     $path
     * @param  array                      $payload
     * @param  callable|null              $modify_request_and_response
     * @return ResponseInterface
     */
    public function executePutRequestAs(AuthenticatedUserInterface $user, $path, $payload = [], callable $modify_request_and_response = null)
    {
        $request = $this->createRequest('PUT', $path, [], $payload);

        /** @var ServerRequestInterface $request */
        /** @var ResponseInterface $response */
        list($request, $response) = $this->prepareRequestAndResponseFor($user, $request);

        return $this->executeRequest($request, $response, $modify_request_and_response);
    }

    /**
     * Execute delete action.
     *
     * @param  string            $path
     * @param  array|null        $payload
     * @param  callable|null     $modify_request_and_response
     * @return ResponseInterface
     */
    public function executeDeleteRequest(string $path, array $payload = null, callable $modify_request_and_response = null)
    {
        return $this->executeRequest($this->createRequest('DELETE', $path, [], $payload), null, $modify_request_and_response);
    }

    /**
     * Execute DELETE request as $user.
     *
     * @param  AuthenticatedUserInterface $user
     * @param  string                     $path
     * @param  array|null                 $payload
     * @param  callable|null              $modify_request_and_response
     * @return ResponseInterface
     */
    public function executeDeleteRequestAs(AuthenticatedUserInterface $user, string $path, array $payload = null, callable $modify_request_and_response = null)
    {
        $request = $this->createRequest('DELETE', $path, [], $payload);

        /** @var ServerRequestInterface $request */
        /** @var ResponseInterface $response */
        list($request, $response) = $this->prepareRequestAndResponseFor($user, $request);

        return $this->executeRequest($request, $response, $modify_request_and_response);
    }

    /**
     * Execute request and optionaly modify request and response.
     *
     * @param  ServerRequestInterface $request
     * @param  ResponseInterface      $response
     * @param  callable|null          $modify_request_and_response
     * @return ResponseInterface
     */
    private function executeRequest(ServerRequestInterface $request, ResponseInterface $response = null, callable $modify_request_and_response = null)
    {
        if ($response === null) {
            $response = new Response();
        }

        if (is_callable($modify_request_and_response)) {
            list($request, $response) = $modify_request_and_response($request, $response);

            if (!$request instanceof RequestInterface || !$response instanceof ResponseInterface) {
                throw new RuntimeException('Request/response modification callback is expected to return a modified request');
            }
        }

        return $this->getAppBoostrapper()->bootstrap()->process($request, $response);
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
            $authenticated_with = $this->createUserSession($user);
        } else {
            $authenticated_with = $this->createToken($user);
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
    protected function createRequest(string $method, string $path, array $query_params = [], array $payload = null): ServerRequestInterface
    {
        $environment_user_data = [
            'REQUEST_METHOD' => $method,
            'REQUEST_URI' => $this->prepareRequestUri($path),
        ];

        if (!empty($query_params)) {
            $environment_user_data['QUERY_STRING'] = http_build_query($query_params);
        }

        $environment = SlimEnvironment::mock($environment_user_data);

        $request = Request::createFromEnvironment($environment)
            ->withParsedBody($payload);

        return $request;
    }

    /**
     * Prepare request URI.
     *
     * @param  string $path
     * @return string
     */
    protected function prepareRequestUri(string $path)
    {
        return '/' . trim($path, '/');
    }

    /**
     * @return ResponseInterface
     */
    protected function createResponse(): ResponseInterface
    {
        return new Response();
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
        return $this->getSessionRepository()->createSession($user, ['remember' => (bool) $remember]);
    }

    /**
     * Create a new token for $user.
     *
     * @param  AuthenticatedUserInterface $user
     * @return TokenInterface
     */
    private function createToken(AuthenticatedUserInterface $user): TokenInterface
    {
        return $this->getTokenRepository()->issueToken($user);
    }

    /**
     * @return AppBootstrapperInterface
     */
    abstract protected function getAppBoostrapper(): AppBootstrapperInterface;

    /**
     * @return SessionRepositoryInterface
     */
    abstract protected function getSessionRepository(): SessionRepositoryInterface;

    /**
     * @return TokenRepositoryInterface
     */
    abstract protected function getTokenRepository(): TokenRepositoryInterface;
}
