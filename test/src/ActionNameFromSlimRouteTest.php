<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Test;

use ActiveCollab\Bootstrap\Controller\ActionNameResolver\ActionNameFromSlimRoute;
use ActiveCollab\Bootstrap\Test\Base\TestCase;
use Slim\Route;

/**
 * @package ActiveCollab\Bootstrap\Test
 */
class ActionNameFromSlimRouteTest extends TestCase
{
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Request attribute 'route' not found in the request.
     */
    public function testRouteNotFound()
    {
        (new ActionNameFromSlimRoute())->getActionName($this->createRequest());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Action name not found for GET method.
     */
    public function testActionForMethodNotFound()
    {
        $route = new Route('GET', '/hello/{name}', function ($req, $resp, $next) {});
        $request = $this->createRequest()
            ->withAttribute('route', $route);

        (new ActionNameFromSlimRoute())->getActionName($request);
    }

    public function testActionForMethod()
    {
        $route = (new Route('GET', '/hello/{name}', function ($req, $resp, $next) {}))
            ->setArgument('GET_action', 'get_value')
            ->setArgument('PUT_action', 'set_value');

        $get_request = $this->createRequest('GET')
            ->withAttribute('route', $route);

        $put_request = $this->createRequest('PUT')
            ->withAttribute('route', $route);

        $action_name_from_route = new ActionNameFromSlimRoute();

        $this->assertSame('get_value', $action_name_from_route->getActionName($get_request));
        $this->assertSame('set_value', $action_name_from_route->getActionName($put_request));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Request attribute 'different_name' not found in the request.
     */
    public function testRouteAttributeNameCanBeChanged()
    {
        $route = (new Route('GET', '/hello/{name}', function ($req, $resp, $next) {}))
            ->setArgument('GET_action', 'get_value');

        $request = $this->createRequest()
            ->withAttribute('route', $route);

        (new ActionNameFromSlimRoute('different_name'))->getActionName($request);
    }

    public function testActionArgumentNameFormatChange()
    {
        $route = (new Route('GET', '/hello/{name}', function ($req, $resp, $next) {}))
            ->setArgument('always_look_here', 'get_value');

        $request = $this->createRequest()
            ->withAttribute('route', $route);

        $action_name_from_route = new ActionNameFromSlimRoute('route', 'always_look_here');

        $this->assertSame('get_value', $action_name_from_route->getActionName($request));
    }
}
