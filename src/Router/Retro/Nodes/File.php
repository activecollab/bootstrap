<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Router\Retro\Nodes;

class File extends Node implements FileInterface
{
    public function isIndex(): bool
    {
        return false;
    }

    public function containsMiddlewares(): bool
    {
        return false;
    }
}
