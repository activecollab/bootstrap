<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Test\Retro;

use ActiveCollab\Bootstrap\Router\Retro\NodeMiddleware\NodeMiddleware;
use ActiveCollab\Bootstrap\Router\Retro\NodeMiddleware\NodeMiddlewareInterface;
use ActiveCollab\Bootstrap\Router\Retro\Sitemap\SitemapInterface;
use ActiveCollab\Bootstrap\Test\Base\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class NodeMiddlewareTest extends TestCase
{
    /**
     * @dataProvider provideDataForMovedToRouteTest
     * @param bool $isMovedPermanently
     * @param int  $expectedStatusCode
     */
    public function testWillRedirectWhenMovedToRoute(
        bool $isMovedPermanently,
        int $expectedStatusCode
    )
    {
        $movedToUrl = 'https://example.com/login';

        /** @var SitemapInterface|MockObject $sitemapMock */
        $sitemapMock = $this->createMock(SitemapInterface::class);
        $sitemapMock
            ->expects($this->once())
            ->method('urlFor')
            ->with('login')
            ->willReturn($movedToUrl);

        $response = $this->getNodeMiddleware(
            [
                SitemapInterface::class => $sitemapMock,
            ]
        )->movedToRoute('login', [], $isMovedPermanently);

        $this->assertSame($expectedStatusCode, $response->getStatusCode());
        $this->assertContains($movedToUrl, $response->getHeaderLine('Location'));
    }

    public function provideDataForMovedToRouteTest()
    {
        return [
            [true, 301],
            [false, 302],
        ];
    }

    /**
     * @dataProvider provideStatusMethods
     * @param string $methodName
     * @param int    $expectedCode
     */
    public function testWillSetStatus(
        string $methodName,
        int $expectedCode
    )
    {
        /** @var ResponseInterface $result */
        $result = $this->getNodeMiddleware()->$methodName();

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame($expectedCode, $result->getStatusCode());
    }

    public function provideStatusMethods(): array
    {
        return [
            ['ok', 200],
            ['badRequest', 400],
            ['forbidden', 403],
            ['notFound', 404],
            ['conflict', 409],
            ['internalError', 500],
        ];
    }

    private function getNodeMiddleware(array $dependencies = []): NodeMiddlewareInterface
    {
        $middleware = new class extends NodeMiddleware
        {
            public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
            {
                return $handler->handle($request);
            }
        };

        /** @var ContainerInterface|MockObject $containerMock */
        $containerMock = $this->createMock(ContainerInterface::class);

        foreach ($dependencies as $key => $value) {
            $containerMock
                ->expects($this->any())
                ->method('get')
                ->with($key)
                ->willReturn($value);
        }

        $middleware->setContainer($containerMock);

        return $middleware;
    }
}
