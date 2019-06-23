<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Test;

use ActiveCollab\Bootstrap\Test\Base\TestCase;

/**
 * @package ActiveCollab\Bootstrap\Test
 */
abstract class RouterTest extends TestCase
{
    //    /**
//     * @var App
//     */
//    private $slim_app;

//    /**
//     * @var Router
//     */
//    private $model_router;

//    /**
//     * Set up test environment.
//     */
//    public function setUp()
//    {
//        parent::setUp();

//        $this->slim_app = new App();
//        $this->model_router = new Router($this->slim_app);
//    }

//    /**
//     * @expectedException \InvalidArgumentException
//     */
//    public function testEmptyModelNameThrowsAnException()
//    {
//        $this->model_router->mapModel('');
//    }

//    /**
//     * Test map model routes.
//     */
//    public function testMapModel()
//    {
//        $this->assertCount(0, $this->slim_app->getContainer()->get('router')->getRoutes());

//        $this->model_router->mapModel('App\Model\User');

//        $this->assertCount(2, $this->slim_app->getContainer()->get('router')->getRoutes());

//        /** @var Route $collection_route */
//        $collection_route = $this->slim_app->getContainer()->get('router')->getRoutes()['route0'];

//        $this->assertEquals(['GET', 'POST'], $collection_route->getMethods());
//        $this->assertEquals('users', $collection_route->getName());

//        /* @var Route $collection_route */
//        $single_route = $this->slim_app->getContainer()->get('router')->getRoutes()['route1'];

//        $this->assertEquals(['GET', 'PUT', 'DELETE'], $single_route->getMethods());
//        $this->assertEquals('user', $single_route->getName());
//    }

//    /**
//     * Test extend collection routes.
//     */
//    public function testExtendCollection()
//    {
//        $this->assertCount(0, $this->slim_app->getContainer()->get('router')->getRoutes());

//        $this->model_router->mapModel('App\Model\User', null, function (Extender $extender) {
//            $extender->extend('staff');
//        });

//        $this->assertCount(3, $this->slim_app->getContainer()->get('router')->getRoutes());

//        /** @var Route $staff_route */
//        $staff_route = $this->slim_app->getContainer()->get('router')->getRoutes()['route2'];

//        $this->assertEquals(['GET'], $staff_route->getMethods());
//        $this->assertEquals('users_staff', $staff_route->getName());
//    }

//    /**
//     * Test extend single routes.
//     */
//    public function testExtendSingle()
//    {
//        $this->assertCount(0, $this->slim_app->getContainer()->get('router')->getRoutes());

//        $this->model_router->mapModel('App\Model\User', null, null, function (Extender $extender) {
//            $extender->extend('accounts');
//        });

//        $this->assertCount(3, $this->slim_app->getContainer()->get('router')->getRoutes());

//        /** @var Route $user_accounts_route */
//        $user_accounts_route = $this->slim_app->getContainer()->get('router')->getRoutes()['route2'];

//        $this->assertEquals(['GET'], $user_accounts_route->getMethods());
//        $this->assertEquals('user_accounts', $user_accounts_route->getName());
//    }
}
