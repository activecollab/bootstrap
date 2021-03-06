<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\AppMetadata;

class AppMetadata implements AppMetadataInterface
{
    private $name = '';

    private $version = '';

    private $path = '';

    public function __construct(string $name, string $version, string $path)
    {
        $this->name = $name;
        $this->version = $version;
        $this->path = $path;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
