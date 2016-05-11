<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Bootstrap\Command\DevCommand\Migrations;

use ActiveCollab\DatabaseMigrations\Command\All as AllMigrationsHelper;

/**
 * @package ActiveCollab\Bootstrap\Command\DevCommand\Migrations
 */
class All extends Command
{
    use AllMigrationsHelper;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->setDescription('List all migrations');
    }
}
