<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Bootstrap\Controller;

use ActiveCollab\Controller\Controller as BaseController;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteInterface;

abstract class Controller extends BaseController
{
    use ControllerTrait;

    protected function getRouteParam(ServerRequestInterface $request, string $param_name, $default = null)
    {
        $route = $request->getAttribute('route');

        if ($route instanceof RouteInterface) {
            return $route->getArgument($param_name, $default);
        }

        return $default;
    }
}
