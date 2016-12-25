<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\TestCase\FullStack;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use Psr\Http\Message\ResponseInterface;

interface WebTestCaseInterface
{
    public function executeGetRequest(string $path, array $query_params = [], callable $modify_request_and_response = null): ResponseInterface;

    public function executeGetRequestAs(AuthenticatedUserInterface $user, string $path, array $query_params = [], callable $modify_request_and_response = null): ResponseInterface;

    public function executePostRequest(string $path, array $payload = [], callable $modify_request_and_response = null): ResponseInterface;

    public function executePostRequestAs(AuthenticatedUserInterface $user, string $path, array $payload = [], callable $modify_request_and_response = null): ResponseInterface;

    public function executePutRequest(string $path, array $payload = [], callable $modify_request_and_response = null): ResponseInterface;

    public function executePutRequestAs(AuthenticatedUserInterface $user, $path, array $payload = [], callable $modify_request_and_response = null): ResponseInterface;

    public function executeDeleteRequest(string $path, array $payload = [], callable $modify_request_and_response = null): ResponseInterface;

    public function executeDeleteRequestAs(AuthenticatedUserInterface $user, string $path, array $payload = [], callable $modify_request_and_response = null): ResponseInterface;
}
