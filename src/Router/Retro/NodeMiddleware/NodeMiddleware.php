<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Router\Retro\NodeMiddleware;

use ActiveCollab\Bootstrap\Router\Retro\Sitemap\SitemapInterface;
use ActiveCollab\ContainerAccess\ContainerAccessInterface\Implementation as ContainerAccessImplementation;
use InvalidArgumentException;
use Laminas\Diactoros\ResponseFactory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Slim\Interfaces\RouteInterface;

abstract class NodeMiddleware implements NodeMiddlewareInterface
{
    use ContainerAccessImplementation;

    private $routeKey;
    private $sitemap;

    public function __construct(string $routeKey = '__route__')
    {
        $this->routeKey = $routeKey;

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

    public function serviceUnavailable(string $reasonPhrase = ''): ResponseInterface
    {
        return $this->status(503, $reasonPhrase);
    }

    public function status(
        int $code,
        string $reasonPhrase = '',
        ResponseInterface $response = null
    ): ResponseInterface
    {
        if (empty($response)) {
            $response = $this->getResponseFactory()->createResponse();
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
            $this->getSitemap()->absoluteUrlFor($routeName, $data),
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
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException(sprintf('URL "%s" is not valid.', $url));
        }

        if (empty($response)) {
            $response = $this->getResponseFactory()->createResponse();
        }

        return $response
            ->withStatus($isMovedPermanently ? 301 : 302)
            ->withHeader('Location', $url);
    }

    protected function getRoute(ServerRequestInterface $request): RouteInterface
    {
        $route = $request->getAttribute($this->routeKey);

        if (!$route instanceof RouteInterface) {
            throw new RuntimeException('Failed to find route in request.');
        }

        return $route;
    }

    protected function isRoute(
        ServerRequestInterface $request,
        string $nodeName,
        string $requestMethod = null
    ): bool
    {
        $route = $this->getRoute($request);

        if ($requestMethod !== null && $request->getMethod() !== $requestMethod) {
            return false;
        }

        if ($route->getArgument(SitemapInterface::NODE_NAME_ROUTE_ARGUMENT) !== $nodeName) {
            return false;
        }

        return true;
    }

    private $responseFactory;

    protected function getResponseFactory(): ResponseFactoryInterface
    {
        if (empty($this->responseFactory)) {
            $this->responseFactory = new ResponseFactory();
        }

        return $this->responseFactory;
    }

    protected function getSitemap(): SitemapInterface
    {
        return $this->getContainer()->get(SitemapInterface::class);
    }
}
