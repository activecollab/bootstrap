<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Router\Retro\Nodes;

interface DirectoryInterface extends NodeInterface
{
    public function isEmpty(): bool;
    public function hasIndex(): bool;
    public function hasMiddleware(): bool;

    public function addSubdirectory(DirectoryInterface ...$directories): void;

    /**
     * @return DirectoryInterface[]
     */
    public function getSubdirectories(): array;
    public function getSubdirectory(string $name): ?DirectoryInterface;
    public function addFiles(FileInterface ...$files): void;

    /**
     * @return FileInterface[]
     */
    public function getFiles(): array;
}
