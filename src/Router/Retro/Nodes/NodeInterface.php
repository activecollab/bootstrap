<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Router\Retro\Nodes;

interface NodeInterface
{
    public function getRoutingRoot(): string;
    public function getNodeName(): string;
    public function getNodePath(): string;
    public function getBasename(): string;
    public function getPath(): string;

    public function isHidden(): bool;
    public function isSystem(): bool;
    public function isVariable(): bool;

    public function getRoute(): ?RouteInterface;
}
