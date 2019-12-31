<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Router\Retro\Sitemap;

use ActiveCollab\Bootstrap\Router\Retro\Nodes\Directory\DirectoryInterface;
use ActiveCollab\Bootstrap\Router\Retro\Pathfinder\PathfinderInterface;
use ActiveCollab\Bootstrap\Router\Retro\Router;
use ActiveCollab\Bootstrap\SitemapPathResolver\SitemapPathResolverInterface;
use ActiveCollab\ContainerAccess\ContainerAccessInterface;
use FastRoute\RouteParser\Std;
use InvalidArgumentException;
use LogicException;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use RuntimeException;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Slim\Interfaces\RouteInterface;

class Sitemap implements SitemapInterface
{
    private $sitemapPathResolver;
    private $pathfinder;
    private $loadedRoutes = [];
    private $isLoaded = false;

    public function __construct(
        SitemapPathResolverInterface $sitemapPathResolver,
        PathfinderInterface $pathfinder
    )
    {
        $this->sitemapPathResolver = $sitemapPathResolver;
        $this->pathfinder = $pathfinder;
    }

    public function getLoadedRoutes(): iterable
    {
        return $this->loadedRoutes;
    }

    public function urlFor(string $routeName, array $data = []): string
    {
        $route = $this->mustGetLoadedRoute($routeName);
        $routeParser = new Std();
        $pattern = $route->getPattern();

        $segments = [];
        $segmentName = '';

        $expressions = array_reverse($routeParser->parse($pattern));
        foreach ($expressions as $expression) {
            foreach ($expression as $segment) {
                if (is_string($segment)) {
                    $segments[] = $segment;
                    continue;
                }

                if (!array_key_exists($segment[0], $data)) {
                    $segments = [];
                    $segmentName = $segment[0];
                    break;
                }

                $segments[] = $data[$segment[0]];

                unset($data[$segment[0]]);
            }

            if (!empty($segments)) {
                break;
            }
        }

        if (empty($segments)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Missing data for URL segment: %s',
                    $segmentName
                )
            );
        }

        $url = implode('', $segments);
        if (!empty($data)) {
            $url .= '?' . http_build_query($data);
        }

        return $url;
    }

    public function loadRoutes(RouteCollectorProxyInterface $app, ContainerInterface $container): iterable
    {
        if ($this->isLoaded) {
            throw new LogicException('Sitemap already loaded.');
        }

        $this->loadDirRoutes(
            $app,
            $container,
            (new Router())->scan($this->sitemapPathResolver->getSitemapPath()),
            ''
        );

        $this->isLoaded = true;

        return $this->loadedRoutes;
    }

    private function loadDirRoutes(
        RouteCollectorProxyInterface $routeCollector,
        ContainerInterface $container,
        DirectoryInterface $directory,
        string $routePrefix
    ): void
    {
        foreach ($directory->getSubdirectories() as $subdirectory) {
            if ($this->pathfinder->hasRoute($subdirectory)) {
                $group = $routeCollector->group(
                    $this->pathfinder->getRoutingPath($subdirectory),
                    function (RouteCollectorProxyInterface $proxy) use ($subdirectory, &$container, $routePrefix) {
                        $this->loadDirRoutes(
                            $proxy,
                            $container,
                            $subdirectory,
                            ($routePrefix ? $routePrefix . '_' : '') . $subdirectory->getNodeName()
                        );
                    }
                );

                $middlewareNode = $subdirectory->getMiddleware();

                if ($middlewareNode) {
                    $middlewares = require $middlewareNode->getPath();

                    if (is_array($middlewares)) {
                        foreach ($middlewares as $middleware) {
                            if ($middleware instanceof MiddlewareInterface) {
                                if ($middleware instanceof ContainerAccessInterface) {
                                    $middleware->setContainer($container);
                                }

                                $group->add($middleware);
                            }
                        }
                    }
                }
            }
        }

        if (empty($routePrefix)) {
            $middlewareNode = $directory->getMiddleware();

            if ($middlewareNode) {
                $middlewares = require $middlewareNode->getPath();

                if (is_array($middlewares)) {
                    foreach ($middlewares as $middleware) {
                        if ($middleware instanceof MiddlewareInterface) {
                            if ($middleware instanceof ContainerAccessInterface) {
                                $middleware->setContainer($container);
                            }

                            $routeCollector->add($middleware);
                        }
                    }
                }
            }
        }

        if ($directory->hasIndex()) {
            $handler = $this->pathfinder->getRouteHandler($directory->getIndex());

            if ($handler) {
                $this->registerLoadedRoute(
                    $routeCollector->any(
                        $this->pathfinder->getRoutingPath($directory->getIndex()),
                        $handler
                    )->setName($routePrefix ? $routePrefix . '_index' : 'index')
                );
            }
        }

        foreach ($directory->getFiles() as $file) {
            if ($file->isIndex()) {
                continue;
            }

            $handler = $this->pathfinder->getRouteHandler($file);

            if ($handler) {
                $this->registerLoadedRoute(
                    $routeCollector->any(
                        $this->pathfinder->getRoutingPath($file),
                        $handler
                    )->setName(($routePrefix ? $routePrefix . '_' : '') . $file->getNodeName())
                );
            }
        }
    }

    private function getLoadedRoute(string $routeName): ?RouteInterface
    {
        return $this->loadedRoutes[$routeName] ?? null;
    }

    private function mustGetLoadedRoute(string $routeName): RouteInterface
    {
        $route = $this->getLoadedRoute($routeName);

        if (empty($route)) {
            throw new RuntimeException(sprintf('Route "%s" not found.', $routeName));
        }

        return $route;
    }

    private function registerLoadedRoute(RouteInterface $route): void
    {
        $this->loadedRoutes[$route->getName()] = $route;
    }

    public function isLoaded(): bool
    {
        return $this->isLoaded;
    }
}
