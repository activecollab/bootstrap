<?php

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Test\Retro;

use ActiveCollab\Bootstrap\Router\Retro\NodeMiddleware\NodeMiddleware;
use ActiveCollab\Bootstrap\Router\Retro\NodeMiddleware\NodeMiddlewareInterface;
use ActiveCollab\Bootstrap\Test\Base\TestCase;
use Laminas\Diactoros\ServerRequestFactory;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PostMethodOverrideTest extends TestCase
{
    public function testWillAllowPostOverrideByDefault()
    {
        $middleware = $this->getNodeMiddleware();

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', 'https://example.org')
            ->withParsedBody(
                [
                    NodeMiddlewareInterface::DEFAULT_POST_OVERRIDE_FIELD_NAME => 'DELETE',
                ]
            );

        /** @var MockObject|RequestHandlerInterface $requestHandler */
        $requestHandler = $this->createMock(RequestHandlerInterface::class);

        $middleware->process($request, $requestHandler);

        $this->assertFalse($middleware->isMethodCallResults['isPost']);
        $this->assertTrue($middleware->isMethodCallResults['isDelete']);
    }

    /**
     * @dataProvider provideDataForDisabledOverrideCheck
     * @param string $checkMethodName
     * @param bool   $expectedCheckResult
     */
    public function testAllowsPostMethodOverrideToBeDisabled(
        string $checkMethodName,
        bool $expectedCheckResult
    ): void
    {
        $middleware = $this->getNodeMiddleware(null);

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', 'https://example.org')
                ->withParsedBody(
                    [
                        NodeMiddlewareInterface::DEFAULT_POST_OVERRIDE_FIELD_NAME => 'DELETE',
                    ]
                );

        /** @var MockObject|RequestHandlerInterface $requestHandler */
        $requestHandler = $this->createMock(RequestHandlerInterface::class);

        $middleware->process($request, $requestHandler);

        $this->assertSame($expectedCheckResult, $middleware->isMethodCallResults[$checkMethodName]);
    }

    public function provideDataForDisabledOverrideCheck(): array
    {
        return [
            ['isPost', true],
            ['isPut', false],
            ['isPatch', false],
            ['isDelete', false],
        ];
    }

    /**
     * @dataProvider provideDataForOverridCheck
     * @param string $postOverride
     * @param array  $payload
     * @param string $checkMethodName
     * @param bool   $expectedCheckResult
     */
    public function testWillOverrideMethod(
        string $postOverride,
        array $payload,
        string $checkMethodName,
        bool $expectedCheckResult
    ): void
    {
        $middleware = $this->getNodeMiddleware($postOverride);

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', 'https://example.org')
                ->withParsedBody($payload);

        /** @var MockObject|RequestHandlerInterface $requestHandler */
        $requestHandler = $this->createMock(RequestHandlerInterface::class);

        $middleware->process($request, $requestHandler);

        $this->assertSame($expectedCheckResult, $middleware->isMethodCallResults[$checkMethodName]);
    }

    public function provideDataForOverridCheck(): array
    {
        return [

            // Override PUT method.
            ['__method_override', ['__method_override' => 'PUT'], 'isPost', false],
            ['__method_override', ['__method_override' => 'PUT'], 'isPut', true],
            ['__method_override', ['__method_override' => 'PUT'], 'isPatch', false],
            ['__method_override', ['__method_override' => 'PUT'], 'isDelete', false],

            // Override PATCH method.
            ['__method_override', ['__method_override' => 'PATCH'], 'isPost', false],
            ['__method_override', ['__method_override' => 'PATCH'], 'isPut', false],
            ['__method_override', ['__method_override' => 'PATCH'], 'isPatch', true],
            ['__method_override', ['__method_override' => 'PATCH'], 'isDelete', false],

            // Override DELETE method.
            ['__method_override', ['__method_override' => 'DELETE'], 'isPost', false],
            ['__method_override', ['__method_override' => 'DELETE'], 'isPut', false],
            ['__method_override', ['__method_override' => 'DELETE'], 'isPatch', false],
            ['__method_override', ['__method_override' => 'DELETE'], 'isDelete', true],

        ];
    }
    
    private function getNodeMiddleware(
        ?string $postMethodOverride = NodeMiddlewareInterface::DEFAULT_POST_OVERRIDE_FIELD_NAME
    ): NodeMiddlewareInterface
    {
        return new class ($postMethodOverride) extends NodeMiddleware
        {
            public $isMethodCallResults = [];

            public function __construct(string $postMethodOverride = null)
            {
                parent::__construct();

                $this->setPostMethodOverride($postMethodOverride);
            }

            public function process(
                ServerRequestInterface $request,
                RequestHandlerInterface $handler
            ): ResponseInterface
            {
                foreach ([
                    'isHead',
                    'isGet',
                    'isPost',
                    'isPut',
                    'isPatch',
                    'isDelete',
                ] as $methodToCall) {
                    $this->isMethodCallResults[$methodToCall] = $this->$methodToCall($request);
                }

                return $handler->handle($request);
            }
        };
    }
}
