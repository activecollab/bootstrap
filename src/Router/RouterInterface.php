<?php

/*
 * This file is part of the Active Collab ID project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Bootstrap\Router;

use Slim\Route;

/**
 * @package ActiveCollab\Bootstrap\Router
 */
interface RouterInterface
{
    /**
     * @param  string $path
     * @param  string $controller_name
     * @param  array  $method_to_action
     * @param  string $name
     * @return Route
     */
    public function map($path, $controller_name, array $method_to_action = [], $name = '');

    /**
     * Prepare a set of routes for a given model.
     *
     * Using $settings you can override following properties:
     *
     * - model_name - Underscore, plural name of the model. If omitted, it will be set based on model class name (last bit)
     * - id - name of the ID variable
     * - id_format - format of the ID variable (numeric value is default value)
     * - controller - name of the controller that needs to be used (full path is needed)
     *
     * @param string        $model_class
     * @param array|null    $settings
     * @param callable|null $extend_collection
     * @param callable|null $extend_single
     */
    public function mapModel($model_class, array $settings = null, callable $extend_collection = null, callable $extend_single = null);
}
