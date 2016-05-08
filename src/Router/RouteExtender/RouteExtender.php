<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Bootstrap\Router\RouteExtender;

use InvalidArgumentException;
use Slim\App;

/**
 * @package ActiveCollab\Bootstrap\Router
 */
class RouteExtender
{
    /**
     * @var App
     */
    private $app;

    /**
     * @var string
     */
    private $controller_namespace;

    /**
     * @var array
     */
    private $settings;

    /**
     * @param App    $app
     * @param string $controller_namespace
     * @param array  $settings
     */
    public function __construct(App &$app, $controller_namespace = 'App\Controllers', array $settings)
    {
        $this->app = $app;
        $this->controller_namespace = $controller_namespace;
        $this->settings = $settings;
    }

    /**
     * {@inheritdoc}
     */
    public function &extend($path, array $method_to_action = [], $name = '', $controller_name = null)
    {
        if (empty($path)) {
            throw new InvalidArgumentException('Route path is required');
        }

        $path = trim($path, '/');

        if (empty($name)) {
            $name = str_replace(['{', '}', '/', '-'], ['', '', '_', '_'], $path);
        }

        if (empty($method_to_action)) {
            $method_to_action = ['GET' => $name];
        }

        if (empty($controller_name)) {
            $controller = $this->settings['controller'];
        } else {
            $controller = strpos($controller_name, '\\') === false ? $this->controller_namespace . '\\' . $controller_name : $controller_name;
        }

        $route = $this->app->map(array_keys($method_to_action), "{$this->settings['path']}/$path", $controller)->setName("{$this->settings['name']}_{$name}");

        foreach ($method_to_action as $method => $action) {
            $route->setArgument("{$method}_action", $action);
        }

        return $route;
    }
}
