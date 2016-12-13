<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Controller;

use ActiveCollab\Bootstrap\Controller\AuthenticationAttributes\AuthenticationAttributesInterface;
use ActiveCollab\Bootstrap\Controller\AuthenticationAttributes\AuthenticationAttributesTrait;
use ActiveCollab\Controller\Controller as BaseController;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteInterface;

abstract class Controller extends BaseController implements AuthenticationAttributesInterface
{
    use AuthenticationAttributesTrait;

    protected function getRouteParam(ServerRequestInterface $request, string $param_name, $default = null)
    {
        $route = $request->getAttribute('route');

        if ($route instanceof RouteInterface) {
            return $route->getArgument($param_name, $default);
        }

        return $default;
    }
}
