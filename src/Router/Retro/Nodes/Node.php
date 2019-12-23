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
    private $node_name = '';
    private $node_path;
    private $path;
    private $basename;
    private $extension = '';
    private $is_hidden = false;
    private $is_system = false;
    private $is_variable = false;

    public function __construct(
        string $routing_root,
        string $node_path
    )
    {
        $this->routing_root = $routing_root;
        $this->node_path = $node_path;

        $this->path = sprintf('%s/%s', $this->routing_root, $this->node_path);
        $this->basename = basename($this->path);

        $bits = explode('.', $this->basename);

        if (empty($bits[0])) {
            $this->is_hidden = true;

            unset($bits[0]);
            $bits = array_values($bits);
        }

        if (count($bits) > 1) {
            $this->extension = $bits[count($bits) - 1];
        }

        if (mb_substr($bits[0], 0, 2) === '__') {
            if (mb_substr($bits[0], -2) === '__') {
                $this->node_name = mb_substr($bits[0], 2, mb_strlen($bits[0]) - 4);
                $this->is_variable = true;
            } else {
                $this->node_name = mb_substr($bits[0], 2);
                $this->is_system = true;
            }
        } else {
            $this->node_name = $bits[0];
        }
    }

    public function getRoutingRoot(): string
    {
        return $this->routing_root;
    }

    public function getNodeName(): string
    {
        return $this->node_name;
    }

    public function getNodePath(): string
    {
        return $this->node_path;
    }

    public function getBasename(): string
    {
        return $this->basename;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function isHidden(): bool
    {
        return $this->is_hidden;
    }

    public function isExecutable(): bool
    {
        return $this->extension === 'php';
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
