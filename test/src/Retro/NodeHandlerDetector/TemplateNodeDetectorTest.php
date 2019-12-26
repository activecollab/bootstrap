<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Test\Retro\NodeHandlerDetector;

use ActiveCollab\Bootstrap\Router\Retro\Handlers\TemplateHandler\TemplateHandlerInterface;
use ActiveCollab\Bootstrap\Router\Retro\Nodes\File\File;
use ActiveCollab\Bootstrap\Router\Retro\Pathfinder\NodeHandlerDetector\TemplateNodeDetector;
use ActiveCollab\Bootstrap\Test\Base\TestCase;
use ActiveCollab\TemplateEngine\TemplateEngineInterface;
use PHPUnit\Framework\MockObject\MockObject;

class TemplateNodeDetectorTest extends TestCase
{
    /**
     * @dataProvider provideTemplateNodeNames
     * @param string $filename
     * @param bool   $expectedTempalteNode
     */
    public function testWillDetectTemplateNode(
        string $filename,
        bool $expectedTempalteNode
    )
    {
        /** @var TemplateEngineInterface|MockObject $templateEngine */
        $templateEngine = $this->createMock(TemplateEngineInterface::class);

        $detector = new TemplateNodeDetector($templateEngine);
        $handler = $detector->probe(new File('/', $filename));

        if ($expectedTempalteNode) {
            $this->assertInstanceOf(TemplateHandlerInterface::class, $handler);
        } else {
            $this->assertNull($handler);
        }
    }

    public function provideTemplateNodeNames(): array
    {
        return [
            ['document.tpl', true],
            ['authors.twig', true],
            ['archive.php', false],
        ];
    }
}
