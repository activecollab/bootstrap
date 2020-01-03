<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Router\Retro\NodeMiddleware;

use ActiveCollab\ContainerAccess\ContainerAccessInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;

interface NodeMiddlewareInterface extends ContainerAccessInterface, MiddlewareInterface
{
    public function ok(string $reasonPhrase = ''): ResponseInterface;
    public function badRequest(string $reasonPhrase = ''): ResponseInterface;
    public function forbidden(string $reasonPhrase = ''): ResponseInterface;
    public function notFound(string $reasonPhrase = ''): ResponseInterface;
    public function conflict(string $reasonPhrase = ''): ResponseInterface;
    public function internalError(string $reasonPhrase = ''): ResponseInterface;
    public function serviceUnavailable(string $reasonPhrase = ''): ResponseInterface;

    public function status(
        int $code,
        string $reasonPhrase = '',
        ResponseInterface $response = null
    ): ResponseInterface;

    public function movedToRoute(
        string $routeName,
        array $data = [],
        bool $isMovedPermanently = false,
        ResponseInterface $response = null
    ): ResponseInterface;

    public function moved(
        string $url,
        bool $isMovedPermanently = false,
        ResponseInterface $response = null
    ): ResponseInterface;
}
