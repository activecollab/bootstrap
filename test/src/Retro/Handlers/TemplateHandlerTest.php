<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Test\Retro\Handlers;

use ActiveCollab\Bootstrap\Router\Retro\Handlers\TemplateHandler\TemplateHandler;
use ActiveCollab\Bootstrap\Test\Base\TestCase;
use ActiveCollab\TemplateEngine\TemplateEngineInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Zend\Diactoros\ResponseFactory;
use Zend\Diactoros\ServerRequestFactory;

class TemplateHandlerTest extends TestCase
{
    public function testWillFetchTemplate()
    {
        $templateName = 'about-us.tpl';
        $templateAttributes = [
            'one' => 1,
            'two' => 2,
            'three' => 3,
        ];

        /** @var TemplateEngineInterface|MockObject $templateEngine */
        $templateEngine = $this->createMock(TemplateEngineInterface::class);
        $templateEngine
            ->expects($this->once())
            ->method('fetch')
            ->with($templateName, $templateAttributes);

        $request = (new ServerRequestFactory())->createServerRequest('GET', '/');
        $response = (new ResponseFactory())->createResponse();

        call_user_func(
            new TemplateHandler($templateEngine, $templateName, $templateAttributes),
            $request,
            $response
        );
    }
}
