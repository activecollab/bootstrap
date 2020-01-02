<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Test\Retro;

use ActiveCollab\Bootstrap\Router\Retro\NodeMiddleware\NodeMiddleware;
use ActiveCollab\Bootstrap\Test\Base\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class NodeMiddlewareTest extends TestCase
{
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
        $middleware = new class extends NodeMiddleware
        {
            public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
            {
                return $handler->handle($request);
            }
        };

        /** @var ResponseInterface $result */
        $result = $middleware->$methodName();

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
}
