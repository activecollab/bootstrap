<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Bootstrap\Command\DevCommand;

use ActiveCollab\Bootstrap\Command\Command;

abstract class DevCommand extends Command
{
    public function getCommandNamePrefix(): string
    {
        return 'dev:';
    }
}
