<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Router\Retro\RouteFactory;

use ActiveCollab\Bootstrap\Router\Retro\Handlers\HandlerInterface;
use ActiveCollab\Bootstrap\Router\Retro\Nodes\NodeInterface;

class RouteFactory
{
    public function hasRoute(NodeInterface $node): bool
    {
        return !$node->isSystem() && !$node->isHidden();
    }

    public function getRouteHandler(NodeInterface $node): HandlerInterface
    {
        return null;
    }
}
