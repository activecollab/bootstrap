<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Bootstrap\TestCase\Utils;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\Session\SessionInterface;
use ActiveCollab\Authentication\Token\TokenInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @package ActiveCollab\Bootstrap\TestCase\Utils
 */
interface RequestExecutorInterface
{
    const SESSION = 'session';
    const TOKEN = 'token';

    public function &as(AuthenticatedUserInterface $user, string $authentication_method = self::SESSION): RequestExecutorInterface;

    public function &usingSession(SessionInterface $session): RequestExecutorInterface;

    public function &usingToken(TokenInterface $token): RequestExecutorInterface;

    public function get(string $path, array $query_params = [], callable $modify_request_and_response = null): ResponseInterface;

    public function post(string $path, array $payload = [], callable $modify_request_and_response = null): ResponseInterface;

    public function put(string $path, array $payload = [], callable $modify_request_and_response = null): ResponseInterface;

    public function delete(string $path, array $payload = [], callable $modify_request_and_response = null): ResponseInterface;
}
