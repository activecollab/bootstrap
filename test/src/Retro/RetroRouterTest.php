<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Test\Retro;

use ActiveCollab\Bootstrap\Router\Retro\Nodes\DirectoryInterface;
use ActiveCollab\Bootstrap\Router\Retro\Router;
use ActiveCollab\Bootstrap\Test\Base\TestCase;
use RuntimeException;

class RetroRouterTest extends TestCase
{
    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Path "not a directory" is not a directory.
     */
    public function testWillThrowExceptionOnMissingDir()
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
}
