<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Bootstrap\TestCase;

use ActiveCollab\Bootstrap\ResultEncoder\ResultEncoder;
use LogicException;
use Slim\Http\Response;
use Slim\HttpCache\Cache;
use Slim\Route;

/**
 * @property \Slim\HttpCache\Cache $cache
 * @property \ActiveCollab\Bootstrap\ResultEncoder\ResultEncoder $result_encoders
 *
 * @package ActiveCollab\Id\Test\Base
 */
abstract class ModelControllerTestCase extends RequestResponseTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->addToContainer('cache', function () {
            return new Cache();
        });
        $this->addToContainer('result_encoder', function ($c) {
            return new ResultEncoder($c['cache'], $c['app_identifier'], $c['user_identifier']);
        });
    }

    /**
     * Dispatch collection action.
     *
     * @param  string   $controller_class
     * @param  string   $method
     * @param  array    $route_arguments
     * @param  array    $query_params
     * @param  array    $body
     * @param  string   $action_name
     * @return Response
     */
    protected function dispatchCollectionAction($controller_class, $method, array $route_arguments = [], array $query_params = [], array $body = [], $action_name = '')
    {
        if (empty($action_name)) {
            if ($method == 'GET') {
                $action_name = 'index';
            } elseif ($method == 'POST') {
                $action_name = 'add';
            } else {
                throw new LogicException("We don't have a default action for '$method' method");
            }
        }

        $route = (new Route([$method], '/', $controller_class))->setArguments($route_arguments)->setArgument("{$method}_action", $action_name);
        $this->assertInstanceOf(Route::class, $route);

        $route->setContainer($this->getContainer());

        return $route->__invoke($this->request->withMethod($method)->withAttribute('route', $route)->withQueryParams($query_params)->withParsedBody($body), new Response());
    }

    /**
     * Dispatch single action.
     *
     * @param  string   $controller_class
     * @param  string   $method
     * @param  array    $route_arguments
     * @param  array    $query_params
     * @param  array    $body
     * @param  string   $action_name
     * @return Response
     */
    protected function dispatchSingleAction($controller_class, $method, array $route_arguments = [], array $query_params = [], array $body = [], $action_name = '')
    {
        if (empty($action_name)) {
            if ($method == 'GET') {
                $action_name = 'view';
            } elseif ($method == 'PUT') {
                $action_name = 'edit';
            } elseif ($method == 'DELETE') {
                $action_name = 'delete';
            } else {
                throw new LogicException("We don't have a default action for '$method' method");
            }
        }

        $route = (new Route([$method], '/', $controller_class))->setArguments($route_arguments)->setArgument("{$method}_action", $action_name);
        $this->assertInstanceOf(Route::class, $route);

        $route->setContainer($this->getContainer());

        return $route->__invoke($this->request->withMethod($method)->withAttribute('route', $route)->withQueryParams($query_params)->withParsedBody($body), new Response());
    }
}
