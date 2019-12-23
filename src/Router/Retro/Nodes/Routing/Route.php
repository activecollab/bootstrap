<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Router\Retro\Nodes\Routing;

use ActiveCollab\Bootstrap\Router\Retro\Nodes\NodeInterface;

class Route implements RouteInterface
{
    private $node;

    public function __construct(NodeInterface $node)
    {
        $this->node = $node;
    }

    public function getFullPath(): string
    {
        return $this->node->getNodePath();
    }
}
