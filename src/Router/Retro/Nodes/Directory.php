<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Router\Retro\Nodes;

class Directory extends Node implements DirectoryInterface
{
    private $subdirectories = [];
    private $files = [];

    public function addSubdirectory(DirectoryInterface ...$directories): void
    {
        foreach ($directories as $directory) {
            $this->subdirectories[$directory->getName()] = $directory;
        }
    }

    public function getSubdirectories(): array
    {
        return $this->subdirectories;
    }

    public function addFiles(FileInterface ...$files): void
    {
        foreach ($files as $file) {
            $this->files[$file->getName()] = $file;
        }
    }

    public function getFiles(): array
    {
        return $this->files;
    }
}
