<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\ClassFinder\ClassDir;

class ClassDir implements ClassDirInterface
{
    private $path;

    private $namespace;

    private $instance_class;

    public function __construct($path, $namespace, $instance_class)
    {
        $this->setPath($path);
        $this->setNamespace($namespace);
        $this->setInstanceClass($instance_class);
    }

    public function getPath(): string
    {
        return $this->path;
    }

    protected function setPath(string $path): ClassDirInterface
    {
        $this->path = $path;

        return $this;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    protected function setNamespace(string $namespace): ClassDirInterface
    {
        $this->namespace = $namespace;

        return $this;
    }

    public function getInstanceClass(): string
    {
        return $this->instance_class;
    }

    protected function setInstanceClass(string $instance_class): ClassDirInterface
    {
        $this->instance_class = $instance_class;

        return $this;
    }
}
