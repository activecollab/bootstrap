<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Test\Retro;

use ActiveCollab\Bootstrap\Router\Retro\Nodes\File\File;
use ActiveCollab\Bootstrap\Router\Retro\Pathfinder\Pathfinder;
use ActiveCollab\Bootstrap\Test\Base\TestCase;

class PathfinderTest extends TestCase
{
    /**
     * @dataProvider provideFileNamesForRouteDetectionTest
     * @param string $filename
     * @param bool   $expected_is_route
     */
    public function testWillDetectIfNodeHasRoute(
        string $filename,
        bool $expected_is_route
    ): void
    {
        $this->assertSame(
            $expected_is_route,
            (new Pathfinder())->hasRoute(
                (new File('/', $filename))
            )
        );
    }

    public function provideFileNamesForRouteDetectionTest(): array
    {
        return [
            ['.hidden', false],
            ['__system', false],
            ['index.twig', true],
            ['__post_id__.php', true],
        ];
    }
}
