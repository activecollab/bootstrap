<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Router\Retro\RequestHandler;

use ActiveCollab\Bootstrap\Router\Retro\Sitemap\SitemapInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;

abstract class RequestHandler implements MiddlewareInterface, RequestHandlerInterface
{
    private $responseFactory;
    private $sitemap;

    public function __construct(ResponseFactoryInterface $responseFactory, SitemapInterface $sitemap)
    {
        $this->responseFactory = $responseFactory;
        $this->sitemap = $sitemap;

        $this->configure();
    }

    protected function configure(): void
    {
    }

    public function ok(string $reasonPhrase = ''): ResponseInterface
    {
        return $this->status(200, $reasonPhrase);
    }

    public function badRequest(string $reasonPhrase = ''): ResponseInterface
    {
        return $this->status(400, $reasonPhrase);
    }

    public function forbidden(string $reasonPhrase = ''): ResponseInterface
    {
        return $this->status(403, $reasonPhrase);
    }

    public function notFound(string $reasonPhrase = ''): ResponseInterface
    {
        return $this->status(404, $reasonPhrase);
    }

    public function conflict(string $reasonPhrase = ''): ResponseInterface
    {
        return $this->status(409, $reasonPhrase);
    }

    public function internalError(string $reasonPhrase = ''): ResponseInterface
    {
        return $this->status(500, $reasonPhrase);
    }

    public function status(
        int $code,
        string $reasonPhrase = '',
        ResponseInterface $response = null
    ): ResponseInterface
    {
        if (empty($response)) {
            $response = $this->responseFactory->createResponse();
        }

        return $response->withStatus($code, $reasonPhrase);
    }

    public function movedToRoute(
        string $routeName,
        array $data = [],
        bool $isMovedPermanently = false,
        ResponseInterface $response = null
    ): ResponseInterface
    {
        return $this->moved(
            $this->sitemap->urlFor($routeName, $data),
            $isMovedPermanently,
            $response
        );
    }

    public function moved(
        string $url,
        bool $isMovedPermanently = false,
        ResponseInterface $response = null
    ): ResponseInterface
    {
        if (empty($response)) {
            $response = $this->responseFactory->createResponse();
        }

        return $response
            ->withStatus($isMovedPermanently ? 301 : 302)
            ->withHeader('Location', $url);
    }
}
