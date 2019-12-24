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
use LogicException;

class Directory extends Node implements DirectoryInterface
{
    private $node_name = '';
    private $is_hidden = false;
    private $is_system = false;
    private $is_variable = false;
    private $subdirectories = [];
    private $files = [];
    private $index_file_basename;
    private $middleware_file_basename;

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
        return !empty($this->index_file_basename);
    }

    public function getIndex(): ?FileInterface
    {
        return !empty($this->index_file_basename)
            ? $this->files[$this->index_file_basename]
            : null;
    }

    public function hasMiddleware(): bool
    {
        return !empty($this->middleware_file_basename);
    }

    public function getMiddleware(): ?FileInterface
    {
        return !empty($this->middleware_file_basename)
            ? $this->files[$this->middleware_file_basename]
            : null;
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

            if ($file->isIndex()) {
                if (!empty($this->index_file_basename)) {
                    throw new LogicException('Only onde index file per directory is supported.');
                }

                $this->index_file_basename = $file->getBasename();
            } elseif ($file->isMiddleware()) {
                if (!empty($this->middleware_file_basename)) {
                    throw new LogicException('Only onde middleware file per directory is supported.');
                }

                $this->middleware_file_basename = $file->getBasename();
            }
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
