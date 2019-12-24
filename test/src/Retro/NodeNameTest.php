<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Test\Retro;

use ActiveCollab\Bootstrap\Router\Retro\Nodes\Directory\Directory;
use ActiveCollab\Bootstrap\Router\Retro\Nodes\File\File;
use ActiveCollab\Bootstrap\Test\Base\TestCase;

class NodeNameTest extends TestCase
{
    /**
     * @dataProvider provideFileNamesForTypeTest
     * @param string $filename
     * @param string $expected_entity_name
     * @param string $expected_extension
     * @param bool   $expected_is_hidden
     * @param bool   $expected_is_executable
     * @param bool   $expected_is_system
     * @param bool   $expected_is_variable
     */
    public function testWillDetectTypeBasedOnFilename(
        string $filename,
        string $expected_entity_name,
        string $expected_extension,
        bool $expected_is_hidden,
        bool $expected_is_executable,
        bool $expected_is_system,
        bool $expected_is_variable
    )
    {
        $routable_file = new File('/', $filename);

        $this->assertSame(
            $expected_entity_name,
            $routable_file->getNodeName()
        );

        $this->assertSame(
            $expected_is_hidden,
            $routable_file->isHidden()
        );

        $this->assertSame(
            $expected_extension,
            $routable_file->getExtension()
        );

        $this->assertSame(
            $expected_is_executable,
            $routable_file->isExecutable()
        );

        $this->assertSame(
            $expected_is_system,
            $routable_file->isSystem()
        );

        $this->assertSame(
            $expected_is_variable,
            $routable_file->isVariable()
        );
    }

    public function provideFileNamesForTypeTest(): array
    {
        return [
            ['.gitignore', 'gitignore', '', true, false, false, false],
            ['.phpunit.xml', 'phpunit', 'xml', true, false, false, false],
            ['authors.html', 'authors', 'html', false, false, false, false],
            ['awesome.handler.php', 'awesome.handler', 'php', false, true, false, false],
            ['index.php', 'index', 'php', false, true, false, false],
            ['__middleware.php', 'middleware', 'php', false, true, true, false],
            ['__post_id__.php', 'post_id', 'php', false, true, false, true],
        ];
    }

    /**
     * @dataProvider provideDirNamesForTypeTest
     * @param string $dirname
     * @param string $expected_entity_name
     * @param bool   $expected_is_hidden
     * @param bool   $expected_is_system
     * @param bool   $expected_is_variable
     */
    public function testWillDetectTypeBasedOnDirname(
        string $dirname,
        string $expected_entity_name,
        bool $expected_is_hidden,
        bool $expected_is_system,
        bool $expected_is_variable
    ): void
    {
        $dir = new Directory('/', $dirname);

        $this->assertSame(
            $expected_entity_name,
            $dir->getNodeName()
        );

        $this->assertSame(
            $expected_is_hidden,
            $dir->isHidden()
        );

        $this->assertSame(
            $expected_is_system,
            $dir->isSystem()
        );

        $this->assertSame(
            $expected_is_variable,
            $dir->isVariable()
        );
    }

    public function provideDirNamesForTypeTest(): array
    {
        return [
            ['.ssh', 'ssh',  true, false, false],
            ['.ssh.important', 'ssh.important', true, false, false],
            ['posts', 'posts', false, false, false],
            ['vip.authors', 'vip.authors', false, false, false],
            ['__templates', 'templates', false, true, false],
            ['__post_id__', 'post_id', false, false, true],
        ];
    }

    /**
     * @dataProvider provideDataForIndexTest
     * @param string $file_name
     * @param bool   $expected_is_index
     */
    public function testWillDetectIndexFile(
        string $file_name,
        bool $expected_is_index
    ): void
    {
        $this->assertSame(
            $expected_is_index,
            (new File('/', $file_name))->isIndex()
        );
    }
    
    public function provideDataForIndexTest(): array
    {
        return [
            ['index', true],
            ['index.php', true],
            ['index.html', true],
            ['index.twig', true],
            ['index.not.html', false],
        ];
    }

    /**
     * @dataProvider provideDataForMiddlewareTest
     * @param string $file_name
     * @param bool   $expected_is_middleware
     */
    public function testWillDetectIsMiddleware(
        string $file_name,
        bool $expected_is_middleware
    ): void
    {
        $this->assertSame(
            $expected_is_middleware,
            (new File('/', $file_name))->isMiddleware()
        );
    }

    public function provideDataForMiddlewareTest(): array
    {
        return [
            ['middleware', false],
            ['__middleware', true],
            ['middleware.php', false],
            ['__middleware.php', true],
            ['__middleware-not.php', false],
        ];
    }
}
