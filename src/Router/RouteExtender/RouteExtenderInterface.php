<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Bootstrap\Router\RouteExtender;

use Slim\Route;

/**
 * @package ActiveCollab\Bootstrap\Router\RouteExtender
 */
interface RouteExtenderInterface
{
    /**
     * Extend the route.
     *
     * $method_to_action should be an array where key is method (GET, POST, PUT etc) and value is action that it maps to
     *
     * If $name is empty, this function will prepare it based on the $path value
     *
     * @param  string      $path
     * @param  array       $method_to_action
     * @param  string      $name
     * @param  string|null $controller_name
     * @return Route
     */
    public function &extend($path, array $method_to_action = [], $name = '', $controller_name = null);
}
