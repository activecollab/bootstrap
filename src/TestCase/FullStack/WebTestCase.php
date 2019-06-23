<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\TestCase\FullStack;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Bootstrap\TestCase\Utils\RequestExecutor;
use ActiveCollab\Bootstrap\TestCase\Utils\RequestExecutorInterface;
use Psr\Http\Message\ResponseInterface;

abstract class WebTestCase extends TestCase implements WebTestCaseInterface
{
    protected function assertJsonResponse($response): void
    {
        /** @var ResponseInterface $response */
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertContains('application/json', $response->getHeaderLine('Content-Type'));
    }

    public function executeGetRequest(string $path, array $query_params = [], callable $modify_request_and_response = null): ResponseInterface
    {
        return $this->getRequestExecutor()
            ->get($path, $query_params, $modify_request_and_response);
    }

    public function executeGetRequestAs(AuthenticatedUserInterface $user, string $path, array $query_params = [], callable $modify_request_and_response = null): ResponseInterface
    {
        return $this->getRequestExecutor()
            ->as($user)
            ->get($path, $query_params, $modify_request_and_response);
    }

    public function executePostRequest(string $path, array $payload = [], callable $modify_request_and_response = null): ResponseInterface
    {
        return $this->getRequestExecutor()
            ->post($path, $payload, $modify_request_and_response);
    }

    public function executePostRequestAs(AuthenticatedUserInterface $user, string $path, array $payload = [], callable $modify_request_and_response = null): ResponseInterface
    {
        return $this->getRequestExecutor()
            ->as($user)
            ->post($path, $payload, $modify_request_and_response);
    }

    public function executePutRequest(string $path, array $payload = [], callable $modify_request_and_response = null): ResponseInterface
    {
        return $this->getRequestExecutor()
            ->put($path, $payload, $modify_request_and_response);
    }

    public function executePutRequestAs(AuthenticatedUserInterface $user, $path, array $payload = [], callable $modify_request_and_response = null): ResponseInterface
    {
        return $this->getRequestExecutor()
            ->as($user)
            ->put($path, $payload, $modify_request_and_response);
    }

    public function executeDeleteRequest(string $path, array $payload = [], callable $modify_request_and_response = null): ResponseInterface
    {
        return $this->getRequestExecutor()
            ->delete($path, $payload, $modify_request_and_response);
    }

    public function executeDeleteRequestAs(AuthenticatedUserInterface $user, string $path, array $payload = [], callable $modify_request_and_response = null): ResponseInterface
    {
        return $this->getRequestExecutor()
            ->as($user)
            ->delete($path, $payload, $modify_request_and_response);
    }

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

    protected function getCookiesPropertyName(): string
    {
        return 'cookies';
    }

    protected function getSessionIdCookieNamePropertyName(): string
    {
        return 'session_id_cookie_name';
    }

    protected function getSessionRepositoryPropertyName(): string
    {
        return 'session_repository';
    }

    protected function getTokenRepositoryPropertyName(): string
    {
        return 'token_repository';
    }
}
