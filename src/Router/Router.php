<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Bootstrap\Router;

use ActiveCollab\Bootstrap\Router\RouteExtender\RouteExtender;
use Doctrine\Common\Inflector\Inflector;
use InvalidArgumentException;
use Slim\App;

/**
 * @package ActiveCollab\Bootstrap\Router
 */
class Router implements RouterInterface
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
     * @param App    $app
     * @param string $controller_namespace
     */
    public function __construct(App &$app, $controller_namespace = 'App\Controller')
    {
        $this->app = $app;
        $this->controller_namespace = $controller_namespace;
    }

    /**
     * {@inheritdoc}
     */
    public function map($path, $controller_name, array $method_to_action = [], $name = '')
    {
        if (empty($path)) {
            throw new InvalidArgumentException('Route path is required');
        }

        $path = trim($path, '/');

        if ($path != '') {
            $path = '/' . $path;
        }

        if (empty($name)) {
            $name = str_replace(['{', '}', '/', '-'], ['', '', '_', '_'], $path);
        }

        $controller = strpos($controller_name, '\\') === false ? $this->controller_namespace . '\\' . $controller_name : $controller_name;

        if (empty($method_to_action)) {
            $method_to_action = ['GET' => $name];
        }

        $route = $this->app->map(array_keys($method_to_action), $path, $controller)->setName($name);

        foreach ($method_to_action as $method => $action) {
            $route->setArgument("{$method}_action", $action);
        }

        return $route;
    }

    /**
     * {@inheritdoc}
     */
    public function mapModel($model_class, array $settings = null, callable $extend_collection = null, callable $extend_single = null)
    {
        if (empty($model_class)) {
            throw new InvalidArgumentException('Model class is required');
        }

        if (empty($settings)) {
            $settings = [];
        }

        if (empty($settings['model_name'])) {
            $model_name = $this->getModelNameFromClass($model_class);
        } else {
            $model_name = $settings['model_name'];
        }

        $controller = empty($settings['controller']) ? $this->controller_namespace . '\\' . Inflector::classify($model_name) : $settings['controller'];
        $id = empty($settings['id']) ? Inflector::singularize($model_name) . '_id' : $settings['id'];
        $id_format = empty($settings['id_format']) ? '[0-9]+' : $settings['id_format'];

        if (empty($settings['collection_path'])) {
            $collection_path = '/' . str_replace('_', '-', $model_name);
        } else {
            $collection_path = '/' . ltrim($settings['collection_path'], '/');
        }

        if (empty($settings['single_path'])) {
            $single_path = $collection_path . '/{' . $id . ':' . $id_format . '}';
        } else {
            $single_path = '/' . ltrim($settings['single_path'], '/');
        }

        $collection_settings = ['name' => $model_name, 'path' => $collection_path, 'controller' => $controller];
        $single_settings = ['name' => Inflector::singularize($collection_settings['name']), 'path' => $single_path, 'controller' => $controller];

        $this->app->map(['GET', 'POST'], $collection_settings['path'], $collection_settings['controller'])->setName($collection_settings['name'])->setArgument('GET_action', 'index')->setArgument('POST_action', 'add');
        $this->app->map(['GET', 'PUT', 'DELETE'], $single_settings['path'], $single_settings['controller'])->setName($single_settings['name'])->setArgument('GET_action', 'view')->setArgument('PUT_action', 'edit')->setArgument('DELETE_action', 'delete');

        if ($extend_collection) {
            call_user_func($extend_collection, new RouteExtender($this->app, $this->controller_namespace, $collection_settings));
        }

        if ($extend_single) {
            call_user_func($extend_single, new RouteExtender($this->app, $this->controller_namespace, $single_settings));
        }
    }

    /**
     * Get model name from model class.
     *
     * @param  string $model_class
     * @return string
     */
    private function getModelNameFromClass($model_class)
    {
        if (($pos = strrpos($model_class, '\\')) !== false) {
            return Inflector::pluralize(Inflector::tableize(substr($model_class, $pos + 1)));
        } else {
            return Inflector::pluralize(Inflector::tableize($model_class));
        }
    }
}
