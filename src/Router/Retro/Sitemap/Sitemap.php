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
use FastRoute\RouteParser\Std;
use InvalidArgumentException;
use LogicException;
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

    public function urlFor(
        string $routeName,
        array $data = [],
        array $queryParams = []
    ): string
    {

        $route = $this->mustGetLoadedRoute($routeName);
        $routeParser = new Std();
        $pattern = $route->getPattern();

        $segments = [];
        $segmentName = '';

        /*
         * $routes is an associative array of expressions representing a route as multiple segments
         * There is an expression for each optional parameter plus one without the optional parameters
         * The most specific is last, hence why we reverse the array before iterating over it
         */
        $expressions = array_reverse($routeParser->parse($pattern));
        foreach ($expressions as $expression) {
            foreach ($expression as $segment) {
                /*
                 * Each $segment is either a string or an array of strings
                 * containing optional parameters of an expression
                 */
                if (is_string($segment)) {
                    $segments[] = $segment;
                    continue;
                }

                /*
                 * If we don't have a data element for this segment in the provided $data
                 * we cancel testing to move onto the next expression with a less specific item
                 */
                if (!array_key_exists($segment[0], $data)) {
                    $segments = [];
                    $segmentName = $segment[0];
                    break;
                }

                $segments[] = $data[$segment[0]];
            }

            /*
             * If we get to this logic block we have found all the parameters
             * for the provided $data which means we don't need to continue testing
             * less specific expressions
             */
            if (!empty($segments)) {
                break;
            }
        }

        if (empty($segments)) {
            throw new InvalidArgumentException('Missing data for URL segment: ' . $segmentName);
        }

        $url = implode('', $segments);
        if ($queryParams) {
            $url .= '?' . http_build_query($queryParams);
        }

        return $url;
    }

    public function loadRoutes(RouteCollectorProxyInterface $app): iterable
    {
        if ($this->isLoaded) {
            throw new LogicException('Sitemap already loaded.');
        }

        $this->loadDirRoutes(
            $app,
            (new Router())->scan($this->sitemapPathResolver->getSitemapPath()),
            ''
        );

        $this->isLoaded = true;

        return $this->loadedRoutes;
    }

    private function loadDirRoutes(
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
                            ($route_prefix ? $route_prefix . '_' : '') . $subdirectory->getNodeName()
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
                $this->registerLoadedRoute(
                    $routeCollector->any(
                        $this->pathfinder->getRoutingPath($directory->getIndex()),
                        $handler
                    )->setName($route_prefix ? $route_prefix . '_index' : 'index')
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
                    )->setName(($route_prefix ? $route_prefix . '_' : '') . $file->getNodeName())
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
