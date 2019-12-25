<?php

namespace ActiveCollab\Bootstrap\Router\Retro\Pathfinder;

interface PathfinderInterface
{
    public function hasRoute(NodeInterface $node): bool;

    public function getRouteHandler(NodeInterface $node): HandlerInterface;
}