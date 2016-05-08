<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Bootstrap\Command\DevCommand;

use ActiveCollab\Bootstrap\Command\Command;

/**
 * @package ActiveCollab\Bootstrap\Command\DevCommand
 */
abstract class DevCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    public function getCommandNamePrefix()
    {
        return 'dev:';
    }
}
