<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Router\Retro\Sitemap;

use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;

interface SitemapInterface
{
    const NODE_NAME_ROUTE_ARGUMENT = 'nodeName';

    public function urlFor(string $routeName, array $data = []): string;
    public function absoluteUrlFor(string $routeName, array $data = []): string;
    public function isLoaded(): bool;
    public function loadRoutes(RouteCollectorProxyInterface $app, ContainerInterface $container): iterable;
    public function getLoadedRoutes(): iterable;
}
