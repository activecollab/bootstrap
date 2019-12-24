<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Test\Retro;

use ActiveCollab\Bootstrap\Router\Retro\Nodes\Directory\DirectoryInterface;
use ActiveCollab\Bootstrap\Router\Retro\Nodes\File\FileInterface;
use ActiveCollab\Bootstrap\Router\Retro\Router;
use ActiveCollab\Bootstrap\Test\Base\TestCase;
use LogicException;
use RuntimeException;

class RetroRouterTest extends TestCase
{
    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Path "not a directory" is not a directory.
     */
    public function testWillThrowExceptionOnMissingDir(): void
    {
        (new Router())->scan('not a directory');
    }

    public function testWillWalkRecursivelyThrougDir(): void
    {
        $blog_example_path = $this->fixtures_dir . '/blog_example';

        $this->assertDirectoryExists($blog_example_path);

        $routing_root = (new Router())->scan($blog_example_path);

        $this->assertInstanceOf(DirectoryInterface::class, $routing_root);

        [
            $dirs,
            $files,
        ] = $this->recursivelyWalk($routing_root);

        $this->assertCount(7, $dirs);
        $this->assertCount(4, $files);
    }

    private function recursivelyWalk(DirectoryInterface $directory): array
    {
        $dirs = [
            $directory->getNodePath(),
        ];
        $files = [];

        foreach ($directory->getSubdirectories() as $subdirectory) {
            [
                $subdir_dirs,
                $subdir_files,
            ] = $this->recursivelyWalk($subdirectory);

            $dirs = array_merge($dirs, $subdir_dirs);
            $files = array_merge($files, $subdir_files);
        }

        foreach ($directory->getFiles() as $file) {
            $files[] = $file->getNodePath();
        }

        return [
            $dirs,
            $files,
        ];
    }

    /**
     * @dataProvider provideDataForDirectoryElementsTest
     * @param string $subdirectory_name
     * @param bool   $is_empty
     * @param bool   $has_index
     * @param bool   $has_middleware
     */
    public function testWillDetectDirectoryElements(
        string $subdirectory_name,
        bool $is_empty,
        bool $has_index,
        bool $has_middleware
    ): void
    {
        $directory_example_path = $this->fixtures_dir . '/directory_example';

        $this->assertDirectoryExists($directory_example_path);

        $routing_root = (new Router())->scan($directory_example_path);

        $subdirectory = $routing_root->getSubdirectory($subdirectory_name);

        $this->assertInstanceOf(DirectoryInterface::class, $subdirectory);

        $this->assertSame($is_empty, $subdirectory->isEmpty());
        $this->assertSame($has_middleware, $subdirectory->hasMiddleware());
        $this->assertSame($has_index, $subdirectory->hasIndex());
    }

    public function provideDataForDirectoryElementsTest(): array
    {
        return [
            ['empty', true, false, false],
            ['with-index', false, true, false],
            ['with-middleware', false, false, true],
            ['with-middleware-and-index', false, true, true],
        ];
    }

    public function testWillReturnIndexFile()
    {
        $directory_example_path = $this->fixtures_dir . '/directory_example/with-index';

        $this->assertDirectoryExists($directory_example_path);

        $routing_root = (new Router())->scan($directory_example_path);

        $this->assertTrue($routing_root->hasIndex());
        $this->assertInstanceOf(FileInterface::class, $routing_root->getIndex());
        $this->assertSame('index', $routing_root->getIndex()->getNodeName());
    }

    public function testWillReturnMiddlewareFile()
    {
        $directory_example_path = $this->fixtures_dir . '/directory_example/with-middleware';

        $this->assertDirectoryExists($directory_example_path);

        $routing_root = (new Router())->scan($directory_example_path);

        $this->assertTrue($routing_root->hasMiddleware());
        $this->assertInstanceOf(FileInterface::class, $routing_root->getMiddleware());
        $this->assertSame('middleware', $routing_root->getMiddleware()->getNodeName());
    }

    public function testWillThrowExceptionOnMultipleIndexes(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Only onde index file per directory is supported.');

        $directory_example_path = $this->fixtures_dir . '/with_multi_index_example';

        $this->assertDirectoryExists($directory_example_path);

        (new Router())->scan($directory_example_path);
    }

    public function testWillThrowExceptionOnMultipleMiddlewares(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Only onde middleware file per directory is supported.');

        $directory_example_path = $this->fixtures_dir . '/with_multi_middleware_example';

        $this->assertDirectoryExists($directory_example_path);

        (new Router())->scan($directory_example_path);
    }
}
