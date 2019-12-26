<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Router\Retro\SitemapLoader;

use Slim\Interfaces\RouteCollectorProxyInterface;

interface SitemapLoaderInterface
{
    public function loadRoutes(RouteCollectorProxyInterface $app): iterable;
}
