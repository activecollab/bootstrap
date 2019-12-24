<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Router\Retro\Nodes\Directory;

use ActiveCollab\Bootstrap\Router\Retro\Nodes\File\FileInterface;
use ActiveCollab\Bootstrap\Router\Retro\Nodes\Node;
use ActiveCollab\Bootstrap\Router\Retro\Nodes\NodeNameParser\NodeNameParser;

class Directory extends Node implements DirectoryInterface
{
    private $node_name = '';
    private $is_hidden = false;
    private $is_system = false;
    private $is_variable = false;
    private $subdirectories = [];
    private $files = [];

    public function __construct(string $routing_root, string $node_path)
    {
        parent::__construct($routing_root, $node_path);

        [
            $this->node_name,
            $this->is_hidden,
            $this->is_system,
            $this->is_variable,
        ] = (new NodeNameParser($this->getBasename()))->getDirectoryProperties();
    }

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

    public function getNodeName(): string
    {
        return $this->node_name;
    }

    public function isHidden(): bool
    {
        return $this->is_hidden;
    }

    public function isSystem(): bool
    {
        return $this->is_system;
    }

    public function isVariable(): bool
    {
        return $this->is_variable;
    }
}
