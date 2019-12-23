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

    public function isEmpty(): bool
    {
        return empty($this->subdirectories) && empty($this->files);
    }

    public function hasIndex(): bool
    {
        return !empty($this->files['index.php']);
    }

    public function hasMiddleware(): bool
    {
        return !empty($this->files['__middleware.php']);
    }

    public function addSubdirectory(DirectoryInterface ...$directories): void
    {
        foreach ($directories as $directory) {
            $this->subdirectories[$directory->getBasename()] = $directory;
        }
    }

    public function getSubdirectories(): array
    {
        return $this->subdirectories;
    }

    public function getSubdirectory(string $name): ?DirectoryInterface
    {
        return $this->subdirectories[$name] ?? null;
    }

    public function addFiles(FileInterface ...$files): void
    {
        foreach ($files as $file) {
            $this->files[$file->getBasename()] = $file;
        }
    }

    public function getFiles(): array
    {
        return $this->files;
    }
}
