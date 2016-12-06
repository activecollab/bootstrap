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
use ActiveCollab\Authentication\Token\RepositoryInterface as TokenRepositoryInterface;
use ActiveCollab\Bootstrap\AppBootstrapper\AppBootstrapperInterface;
use ActiveCollab\Bootstrap\TestCase\Utils\RequestExecutor;
use ActiveCollab\Bootstrap\TestCase\Utils\RequestExecutorInterface;
use ActiveCollab\Cookies\CookiesInterface;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @package ActiveCollab\Bootstrap\TestCase
 */
abstract class FullStackTestCase extends \PHPUnit_Framework_TestCase
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
        return $this->getRequestExecutor()
            ->get($path, $query_params, $modify_request_and_response);
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
        return $this->getRequestExecutor()
            ->as($user)
            ->get($path, $query_params, $modify_request_and_response);
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
        return $this->getRequestExecutor()
            ->post($path, $payload, $modify_request_and_response);
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
        return $this->getRequestExecutor()
            ->as($user)
            ->post($path, $payload, $modify_request_and_response);
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
        return $this->getRequestExecutor()
            ->put($path, $payload, $modify_request_and_response);
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
        return $this->getRequestExecutor()
            ->as($user)
            ->put($path, $payload, $modify_request_and_response);
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
        return $this->getRequestExecutor()
            ->delete($path, $payload, $modify_request_and_response);
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
        return $this->getRequestExecutor()
            ->as($user)
            ->delete($path, $payload, $modify_request_and_response);
    }

    /**
     * @return RequestExecutorInterface
     */
    private function getRequestExecutor(): RequestExecutorInterface
    {
        $app_bootstrapper = $this->getAppBootstrapper();

        if (!$app_bootstrapper->isBootstrapped()) {
            $app_bootstrapper->bootstrap();
        }

        $container = $app_bootstrapper->getApp()->getContainer();

        return new RequestExecutor(
            $app_bootstrapper,
            $this->getSessionRepository($container),
            $this->getTokenRepository($container),
            $this->getCookies($container),
            $this->getSessionIdCookieName($container)
        );
    }

    /**
     * @return AppBootstrapperInterface
     */
    abstract protected function getAppBootstrapper(): AppBootstrapperInterface;

    /**
     * @param  ContainerInterface $container
     * @return CookiesInterface
     */
    abstract protected function getCookies(ContainerInterface $container): CookiesInterface;

    /**
     * @param  ContainerInterface $container
     * @return string
     */
    abstract protected function getSessionIdCookieName(ContainerInterface $container): string;

    /**
     * @param  ContainerInterface         $container
     * @return SessionRepositoryInterface
     */
    abstract protected function getSessionRepository(ContainerInterface $container): SessionRepositoryInterface;

    /**
     * @param  ContainerInterface       $container
     * @return TokenRepositoryInterface
     */
    abstract protected function getTokenRepository(ContainerInterface $container): TokenRepositoryInterface;
}
