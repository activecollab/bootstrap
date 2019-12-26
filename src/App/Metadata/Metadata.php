<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\App\Metadata;

class Metadata implements MetadataInterface
{
    private $name = '';
    private $version = '';
    private $path = '';
    private $url = '';

    public function __construct(
        string $name,
        string $version,
        string $path,
        string $url
    )
    {
        $this->name = $name;
        $this->version = $version;
        $this->path = $path;
        $this->url = $url;
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

    public function getUrl(): string
    {
        return $this->url;
    }
}
