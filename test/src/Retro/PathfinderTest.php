<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Test\Retro;

use ActiveCollab\Bootstrap\Router\Retro\Nodes\Directory\Directory;
use ActiveCollab\Bootstrap\Router\Retro\Nodes\Directory\DirectoryInterface;
use ActiveCollab\Bootstrap\Router\Retro\Nodes\File\File;
use ActiveCollab\Bootstrap\Router\Retro\Nodes\File\FileInterface;
use ActiveCollab\Bootstrap\Router\Retro\Nodes\NodeInterface;
use ActiveCollab\Bootstrap\Router\Retro\Pathfinder\Pathfinder;
use ActiveCollab\Bootstrap\Test\Base\TestCase;
use RuntimeException;

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

    /**
     * @dataProvider provideNodesForRoutingStringTest
     * @param string $node_type
     * @param string $node_path
     * @param string $expected_routing_string
     */
    public function testWillExtractRoutingStringFromNode(
        string $node_type,
        string $node_path,
        ?string $expected_routing_string
    )
    {
        switch ($node_type) {
            case FileInterface::class:
                $node = new File('/', $node_path);
                break;
            case DirectoryInterface::class:
                $node = new Directory('/', $node_path);
                break;
            default:
                throw new RuntimeException(sprintf('Invalid node type %s.', $node_type));
        }

        $this->assertSame(
            $expected_routing_string,
            (new Pathfinder())->getRoutingPath($node)
        );
    }

    public function provideNodesForRoutingStringTest(): array
    {
        return [
            [FileInterface::class, '.hidden', null],
            [DirectoryInterface::class, '.ssh', null],

            [FileInterface::class, '__middleware', null],
            [DirectoryInterface::class, '__layouts', null],

            [FileInterface::class, 'index.php', '/'],
            [FileInterface::class, 'authors.php', '/authors'],
            [DirectoryInterface::class, 'archive', '/archive'],

            [FileInterface::class, '__post_id__', '/{post_id}'],
            [DirectoryInterface::class, '__post_id__', '/{post_id}'],
        ];
    }

    /**
     * @dataProvider provideDataForFullPathTest
     * @param string|null     $expected_route
     * @param NodeInterface[] $nodes
     */
    public function testWillResolveFullRoutingPath(
        ?string $expected_route,
        array $nodes
    )
    {
        $this->assertSame(
            $expected_route,
            (new Pathfinder())->getRoutingPath(...$nodes)
        );
    }

    public function provideDataForFullPathTest(): array
    {
        return [

            // Only hidden.
            [
                null,
                [
                    new File('/', '.gitignore'),
                ],
            ],

            // Hidden in a directory.
            [
                null,
                [
                    new Directory('/', 'system-stuff'),
                    new File('/', '.gitignore'),
                ],
            ],

            // Only middleware.
            [
                null,
                [
                    new File('/', '__middleware.php'),
                ],
            ],

            // Middleware in a directory.
            [
                null,
                [
                    new Directory('/', 'system-stuff'),
                    new File('/', '__middleware.php'),
                ],
            ],

            // System directory in the structure.
            [
                null,
                [
                    new Directory('/', 'path'),
                    new Directory('/', '__to'),
                    new Directory('/', 'dir'),
                    new File('/', 'authors.tpl'),
                ],
            ],

            // Hidden directory in the structure.
            [
                null,
                [
                    new Directory('/', 'path'),
                    new Directory('/', '.ssh'),
                    new Directory('/', 'dir'),
                    new File('/', 'authors.tpl'),
                ],
            ],

            // Index in a couple of directories.
            [
                '/path/to/dir',
                [
                    new Directory('/', 'path'),
                    new Directory('/', 'to'),
                    new Directory('/', 'dir'),
                    new File('/', 'index.php'),
                ],
            ],

            // Template in a couple of directories.
            [
                '/path/to/dir/authors',
                [
                    new Directory('/', 'path'),
                    new Directory('/', 'to'),
                    new Directory('/', 'dir'),
                    new File('/', 'authors.tpl'),
                ],
            ],

            // Variable directory name.
            [
                '/path/{to}/dir/authors',
                [
                    new Directory('/', 'path'),
                    new Directory('/', '__to__'),
                    new Directory('/', 'dir'),
                    new File('/', 'authors.tpl'),
                ],
            ],

            // Variable file and variable dir.
            [
                '/path/{to}/dir/{post_id}',
                [
                    new Directory('/', 'path'),
                    new Directory('/', '__to__'),
                    new Directory('/', 'dir'),
                    new File('/', '__post_id__.tpl'),
                ],
            ],

        ];
    }
}
