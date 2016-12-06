<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Bootstrap\TestCase\Utils;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @package ActiveCollab\Bootstrap\TestCase\Utils
 */
interface RequestExecutorInterface
{
    const SESSION = 'session';
    const TOKEN = 'token';

    /**
     * @param  AuthenticatedUserInterface $user
     * @param  string                     $authentication_method
     * @return RequestExecutorInterface
     */
    public function &as(AuthenticatedUserInterface $user, string $authentication_method = self::SESSION): RequestExecutorInterface;

    public function get(string $path, array $query_params = [], callable $modify_request_and_response = null): ResponseInterface;

    public function post(string $path, array $payload = [], callable $modify_request_and_response = null): ResponseInterface;

    public function put(string $path, array $payload = [], callable $modify_request_and_response = null): ResponseInterface;

    public function delete(string $path, array $payload = [], callable $modify_request_and_response = null): ResponseInterface;
}
