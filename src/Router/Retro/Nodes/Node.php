<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Router\Retro\Nodes;

abstract class Node implements NodeInterface
{
    private $routing_root;
    private $node_path;
    private $path;
    private $name;

    public function __construct(
        string $routing_root,
        string $node_path
    )
    {
        $this->routing_root = $routing_root;
        $this->node_path = $node_path;

        $this->path = sprintf('%s/%s', $this->routing_root, $this->node_path);
        $this->name = basename($this->path);
    }

    public function getRoutingRoot(): string
    {
        return $this->routing_root;
    }

    public function getNodePath(): string
    {
        return $this->node_path;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}