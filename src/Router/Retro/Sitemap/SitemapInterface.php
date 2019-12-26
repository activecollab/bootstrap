<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Router\Retro\Sitemap;

use Slim\Interfaces\RouteCollectorProxyInterface;

interface SitemapInterface
{
    public function getLoadedRoutes(): iterable;
    public function loadRoutes(RouteCollectorProxyInterface $app): iterable;
    public function isLoaded(): bool;
}
