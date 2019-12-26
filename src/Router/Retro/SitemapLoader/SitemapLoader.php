<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Router\Retro\SitemapLoader;

use ActiveCollab\Bootstrap\Router\Retro\Nodes\Directory\DirectoryInterface;
use ActiveCollab\Bootstrap\Router\Retro\Pathfinder\PathfinderInterface;
use ActiveCollab\Bootstrap\Router\Retro\Router;
use ActiveCollab\Bootstrap\SitemapPathResolver\SitemapPathResolverInterface;
use ActiveCollab\TemplateEngine\TemplateEngineInterface;
use Psr\Http\Server\MiddlewareInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;

class SitemapLoader implements SitemapLoaderInterface
{
    private $sitemapPathResolver;
    private $pathfinder;
    private $templateEngine;

    public function __construct(
        SitemapPathResolverInterface $sitemapPathResolver,
        PathfinderInterface $pathfinder,
        TemplateEngineInterface $templateEngine
    )
    {
        $this->sitemapPathResolver = $sitemapPathResolver;
        $this->pathfinder = $pathfinder;
        $this->templateEngine = $templateEngine;
    }

    public function loadRoutes(RouteCollectorProxyInterface $app): void
    {
        $routingRoot = (new Router())->scan($this->sitemapPathResolver->getSitemapPath());

        $this->loadDirRoutes($app, $routingRoot, '');
    }

    public function loadDirRoutes(
        RouteCollectorProxyInterface $routeCollector,
        DirectoryInterface $directory,
        string $route_prefix
    ): void
    {
        foreach ($directory->getSubdirectories() as $subdirectory) {
            if ($this->pathfinder->hasRoute($subdirectory)) {
                $routeCollector->group(
                    $this->pathfinder->getRoutingPath($subdirectory),
                    function (RouteCollectorProxyInterface $proxy) use ($subdirectory, $route_prefix) {
                        $this->loadDirRoutes(
                            $proxy,
                            $subdirectory,
                            ($route_prefix ? $route_prefix . '_' : '') . $subdirectory->getNodeName() . '_'
                        );
                    }
                );
            }
        }

        $middlewareNode = $directory->getMiddleware();

        if ($middlewareNode) {
            $middlewares = require $middlewareNode->getPath();

            if (is_array($middlewares)) {
                foreach ($middlewares as $middleware) {
                    if ($middleware instanceof MiddlewareInterface) {
                        $routeCollector->add($middleware);
                    }
                }
            }
        }

        if ($directory->hasIndex()) {
            $handler = $this->pathfinder->getRouteHandler($directory->getIndex());

            if ($handler) {
                $routeCollector->any(
                    $this->pathfinder->getRoutingPath($directory->getIndex()),
                    $handler
                )->setName($route_prefix ?? 'homepage');
            }
        }

        foreach ($directory->getFiles() as $file) {
            if ($file->isIndex()) {
                continue;
            }

            $handler = $this->pathfinder->getRouteHandler($file);

            if ($handler) {
                $routeCollector->any(
                    $this->pathfinder->getRoutingPath($file),
                    $handler
                )->setName($route_prefix . $file->getNodeName());
            }
        }
    }
}
