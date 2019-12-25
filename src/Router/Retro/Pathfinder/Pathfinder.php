<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Router\Retro\Pathfinder;

use ActiveCollab\Bootstrap\Router\Retro\Handlers\HandlerInterface;
use ActiveCollab\Bootstrap\Router\Retro\Nodes\File\FileInterface;
use ActiveCollab\Bootstrap\Router\Retro\Nodes\NodeInterface;

class Pathfinder implements PathfinderInterface
{
    public function hasRoute(NodeInterface $node): bool
    {
        return !$node->isSystem() && !$node->isHidden();
    }

    public function getRoutingPath(NodeInterface ...$nodes): ?string
    {
        $last_node = end($nodes);

        if ($last_node instanceof NodeInterface && !$this->hasRoute($last_node)) {
            return null;
        }

        $path = [];

        foreach ($nodes as $node) {
            if (!$this->hasRoute($node)) {
                return null;
            }

            $path[] = $this->getRoutingPathForNode($node);
        }

        return '/' . trim(implode('/', $path), '/');
    }

    private function getRoutingPathForNode(NodeInterface $node): string
    {
        if ($node instanceof FileInterface && $node->isIndex()) {
            return '/';
        }

        if ($node->isVariable()) {
            return '{' . $node->getNodeName() . '}';
        }

        return $node->getNodeName();
    }

    public function getRouteHandler(NodeInterface $node): ?HandlerInterface
    {
        if (!$this->hasRoute($node)) {
            return null;
        }

        return null;
    }
}
