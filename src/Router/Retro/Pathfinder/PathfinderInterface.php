<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Router\Retro\Pathfinder;

use ActiveCollab\Bootstrap\Router\Retro\Handlers\HandlerInterface;
use ActiveCollab\Bootstrap\Router\Retro\Nodes\NodeInterface;

interface PathfinderInterface
{
    public function hasRoute(NodeInterface $node): bool;
    public function getRoutingPath(NodeInterface $node): string;
    public function getRouteHandler(NodeInterface $node): HandlerInterface;
}
