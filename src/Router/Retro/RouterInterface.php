<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Router\Retro;

use ActiveCollab\Bootstrap\Router\Retro\Nodes\Directory\DirectoryInterface;

interface RouterInterface
{
    public function scan(string $routing_root): DirectoryInterface;
}
