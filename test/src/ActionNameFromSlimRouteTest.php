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
use Psr\Http\Message\ResponseFactoryInterface;
use RuntimeException;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Routing\Route;

class ActionNameFromSlimRouteTest extends TestCase
{
    public function testRouteNotFound()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Request attribute 'route' not found in the request.");

        (new ActionNameFromSlimRoute())->getActionName($this->createRequest());
    }

    public function testActionForMethodNotFound()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Action name not found for GET method.");

        $response_factory_mock = $this->createMock(ResponseFactoryInterface::class);
        $callable_resolver_mock = $this->createMock(CallableResolverInterface::class);

        $route = new Route(
            ['GET'],
            '/hello/{name}',
            function ($req, $resp, $next) {
            },
            $response_factory_mock,
            $callable_resolver_mock
        );
        $request = $this->createRequest()
            ->withAttribute('route', $route);

        (new ActionNameFromSlimRoute())->getActionName($request);
    }

    public function testActionForMethod()
    {
        $response_factory_mock = $this->createMock(ResponseFactoryInterface::class);
        $callable_resolver_mock = $this->createMock(CallableResolverInterface::class);

        $route = (new Route(
            ['GET'],
            '/hello/{name}',
            function ($req, $resp, $next) {
            },
            $response_factory_mock,
            $callable_resolver_mock
        ))
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

    public function testRouteAttributeNameCanBeChanged()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Request attribute 'different_name' not found in the request.");

        $response_factory_mock = $this->createMock(ResponseFactoryInterface::class);
        $callable_resolver_mock = $this->createMock(CallableResolverInterface::class);

        $route = (new Route(
            ['GET'],
            '/hello/{name}',
            function ($req, $resp, $next) {
            },
            $response_factory_mock,
            $callable_resolver_mock
        ))
            ->setArgument('GET_action', 'get_value');

        $request = $this->createRequest()
            ->withAttribute('route', $route);

        (new ActionNameFromSlimRoute('different_name'))->getActionName($request);
    }

    public function testActionArgumentNameFormatChange()
    {
        $response_factory_mock = $this->createMock(ResponseFactoryInterface::class);
        $callable_resolver_mock = $this->createMock(CallableResolverInterface::class);

        $route = (new Route(
            ['GET'],
            '/hello/{name}',
            function ($req, $resp, $next) {
            },
            $response_factory_mock,
            $callable_resolver_mock
        ))
            ->setArgument('always_look_here', 'get_value');

        $request = $this->createRequest()
            ->withAttribute('route', $route);

        $action_name_from_route = new ActionNameFromSlimRoute(
            'route',
            'always_look_here'
        );

        $this->assertSame('get_value', $action_name_from_route->getActionName($request));
    }
}
