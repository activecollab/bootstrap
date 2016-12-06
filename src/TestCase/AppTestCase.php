<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\TestCase;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Bootstrap\AppBootstrapper\AppBootstrapperInterface;
use ActiveCollab\Bootstrap\TestCase\Utils\NowTrait;
use ActiveCollab\Bootstrap\TestCase\Utils\RequestExecutor;
use ActiveCollab\Bootstrap\TestCase\Utils\RequestExecutorInterface;
use ActiveCollab\DateValue\DateTimeValue;
use Psr\Http\Message\ResponseInterface;

/**
 * @package ActiveCollab\Bootstrap\TestCase
 */
abstract class AppTestCase extends \PHPUnit_Framework_TestCase
{
    use NowTrait;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->setNow(new DateTimeValue());
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $this->setNow(null);

        parent::tearDown();
    }

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
     * @param  array             $payload
     * @param  callable|null     $modify_request_and_response
     * @return ResponseInterface
     */
    public function executePostRequest(string $path, array $payload = [], callable $modify_request_and_response = null)
    {
        return $this->getRequestExecutor()
            ->post($path, $payload, $modify_request_and_response);
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
    public function executePostRequestAs(AuthenticatedUserInterface $user, string $path, array $payload = [], callable $modify_request_and_response = null)
    {
        return $this->getRequestExecutor()
            ->as($user)
            ->post($path, $payload, $modify_request_and_response);
    }

    /**
     * Execute POST request.
     *
     * @param  string            $path
     * @param  array             $payload
     * @param  callable|null     $modify_request_and_response
     * @return ResponseInterface
     */
    public function executePutRequest(string $path, array $payload = [], callable $modify_request_and_response = null)
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
    public function executePutRequestAs(AuthenticatedUserInterface $user, $path, array $payload = [], callable $modify_request_and_response = null)
    {
        return $this->getRequestExecutor()
            ->as($user)
            ->put($path, $payload, $modify_request_and_response);
    }

    /**
     * Execute delete action.
     *
     * @param  string            $path
     * @param  array             $payload
     * @param  callable|null     $modify_request_and_response
     * @return ResponseInterface
     */
    public function executeDeleteRequest(string $path, array $payload = [], callable $modify_request_and_response = null)
    {
        return $this->getRequestExecutor()
            ->delete($path, $payload, $modify_request_and_response);
    }

    /**
     * Execute DELETE request as $user.
     *
     * @param  AuthenticatedUserInterface $user
     * @param  string                     $path
     * @param  array                      $payload
     * @param  callable|null              $modify_request_and_response
     * @return ResponseInterface
     */
    public function executeDeleteRequestAs(AuthenticatedUserInterface $user, string $path, array $payload = [], callable $modify_request_and_response = null)
    {
        return $this->getRequestExecutor()
            ->as($user)
            ->delete($path, $payload, $modify_request_and_response);
    }

    /**
     * @return RequestExecutorInterface
     */
    protected function getRequestExecutor(): RequestExecutorInterface
    {
        $app_bootstrapper = $this->getAppBootstrapper();

        if (!$app_bootstrapper->isBootstrapped()) {
            $app_bootstrapper->bootstrap();
        }

        return new RequestExecutor(
            $app_bootstrapper,
            $this->{$this->getSessionRepositoryPropertyName()},
            $this->{$this->getTokenRepositoryPropertyName()},
            $this->{$this->getCookiesPropertyName()},
            $this->{$this->getSessionIdCookieNamePropertyName()}
        );
    }

    /**
     * @return AppBootstrapperInterface
     */
    abstract protected function getAppBootstrapper(): AppBootstrapperInterface;

    /**
     * @return string
     */
    protected function getCookiesPropertyName(): string
    {
        return 'cookies';
    }

    /**
     * @return string
     */
    protected function getSessionIdCookieNamePropertyName(): string
    {
        return 'session_id_cookie_name';
    }

    /**
     * @return string
     */
    protected function getSessionRepositoryPropertyName(): string
    {
        return 'session_repository';
    }

    /**
     * @return string
     */
    protected function getTokenRepositoryPropertyName(): string
    {
        return 'token_repository';
    }

    /**
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        $app_boostrapper = $this->getAppBootstrapper();

        if (!$app_boostrapper->isBootstrapped()) {
            $app_boostrapper->bootstrap();
        }

        if ($app_boostrapper->getApp()->getContainer()->has($name)) {
            return $app_boostrapper->getApp()->getContainer()->get($name);
        }

        throw new \RuntimeException(sprintf('Property %s not found in class %s', $name, get_class($this)));
    }
}
