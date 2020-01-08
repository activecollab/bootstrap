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
use ActiveCollab\Cookies\CookiesInterface;
use Laminas\Diactoros\RequestFactory;
use Laminas\Diactoros\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

class RequestExecutor implements RequestExecutorInterface
{
    private $app_bootstrapper;
    private $session_repository;
    private $token_repository;
    private $cookies;
    private $session_id_cookie_name;
    private $impersonate_user;
    private $authentication_method = self::SESSION;

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

    public function as(
        AuthenticatedUserInterface $user,
        string $authentication_method = self::SESSION
    ): RequestExecutorInterface
    {
        $this->impersonate_user = $user;
        $this->authentication_method = $authentication_method;

        return $this;
    }

    private $using_session;

    public function usingSession(SessionInterface $session): RequestExecutorInterface
    {
        $this->using_session = $session;

        return $this;
    }

    private $using_token;

    public function usingToken(TokenInterface $token): RequestExecutorInterface
    {
        $this->using_token = $token;

        return $this;
    }

    public function get(
        string $path,
        array $query_params = [],
        callable $modify_request = null
    ): ResponseInterface
    {
        return $this->executeRequest(
            $this->createRequest(
                'GET',
                $path,
                $query_params
            ),
            null,
            $modify_request
        );
    }

    public function post(
        string $path,
        array $payload = [],
        callable $modify_request = null
    ): ResponseInterface
    {
        return $this->executeRequest(
            $this->createRequest(
                'POST',
                $path,
                [],
                $payload
            ),
            null,
            $modify_request
        );
    }

    public function put(
        string $path,
        array $payload = [],
        callable $modify_request = null
    ): ResponseInterface
    {
        return $this->executeRequest(
            $this->createRequest(
                'PUT',
                $path,
                [],
                $payload
            ),
            null,
            $modify_request
        );
    }

    public function delete(
        string $path,
        array $payload = [],
        callable $modify_request = null
    ): ResponseInterface
    {
        return $this->executeRequest(
            $this->createRequest(
                'DELETE',
                $path,
                [],
                $payload
            ),
            null,
            $modify_request
        );
    }

    private function executeRequest(
        ServerRequestInterface $request,
        ResponseInterface $response = null,
        callable $modify_request_and_response = null
    ): ResponseInterface
    {
        if (!$response) {
            $response = $this->createResponse();
        }

        if ($this->impersonate_user) {
            [
                $request,
                $response,
            ] = $this->prepareRequestAndResponseFor(
                $this->impersonate_user,
                $request,
                $response,
                $this->authentication_method === self::SESSION
            );

            if (!$request instanceof ServerRequestInterface || !$response instanceof ResponseInterface) {
                throw new RuntimeException('Request/response modification callback is expected to return a modified request');
            }
        }

        if (is_callable($modify_request_and_response)) {
            [
                $request,
                $response,
            ] = $modify_request_and_response($request, $response);

            if (!$request instanceof ServerRequestInterface || !$response instanceof ResponseInterface) {
                throw new RuntimeException('Request/response modification callback is expected to return a modified request');
            }
        }

        return $this->app_bootstrapper->handle($request);
    }
    private function prepareRequestAndResponseFor(
        AuthenticatedUserInterface $user,
        ServerRequestInterface $request,
        ResponseInterface $response = null,
        $use_session = true
    )
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
            [$request, $response] = $this->cookies->set(
                $request,
                $response,
                $this->session_id_cookie_name,
                $authenticated_with->getSessionId()
            );
        } else {
            $request = $request->withHeader(
                'Authorization',
                'Bearer ' . $authenticated_with->getTokenId()
            );
        }

        return [
            $request,
            $response,
        ];
    }

    private function createRequest(
        string $method,
        string $path,
        array $query_params = [],
        array $payload = null
    ): ServerRequestInterface
    {
        return (new RequestFactory())
            ->createRequest($method, $this->prepareRequestUri($path, $query_params))
                ->withParsedBody($payload);
    }

    private function prepareRequestUri(string $path, array $query_params): string
    {
        $result = '/' . trim($path, '/');

        if (!empty($query_params)) {
            $result .= '?' . http_build_query($query_params);
        }

        return $result;
    }

    protected function createResponse(): ResponseInterface
    {
        return (new ResponseFactory())->createResponse();
    }

    private function createUserSession(AuthenticatedUserInterface $user, bool $remember = false): SessionInterface
    {
        return $this->session_repository->createSession($user, ['remember' => (bool) $remember]);
    }

    private function createToken(AuthenticatedUserInterface $user): TokenInterface
    {
        return $this->token_repository->issueToken($user);
    }
}
