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

class RetroRouterTest extends TestCase
{
    private $blog_example_dir;

    protected function setUp()
    {
        parent::setUp();

        $this->blog_example_dir = dirname(dirname(__DIR__)) . '/fixtures/blog_example';
        $this->assertDirectoryExists($this->blog_example_dir);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Path "not a directory" is not a directory.
     */
    public function testWillThrowExceptionOnMissingDir()
    {
        (new Router())->scan('not a directory');
    }

    public function testWillWalkRecursivelyThrougDir(): void
    {
        $routing_root = (new Router())->scan($this->blog_example_dir);

        $this->assertInstanceOf(DirectoryInterface::class, $routing_root);

        [
            $dirs,
            $files,
        ] = $this->recursivelyWalk($routing_root);

        $this->assertCount(6, $dirs);
        $this->assertCount(4, $files);
    }

    private function recursivelyWalk(DirectoryInterface $directory): array
    {
        $dirs = [];
        $files = [];

        foreach ($directory->getSubdirectories() as $subdirectory) {
            $dirs[] = $subdirectory->getNodePath();

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

        return [$dirs, $files];
    }
}
