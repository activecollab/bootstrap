<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Bootstrap\Command\DevCommand\Migrations;

use ActiveCollab\DatabaseMigrations\Command\Up as UpMigrationsHelper;

/**
 * @package ActiveCollab\Bootstrap\Command\DevCommand\Migrations
 */
class Up extends Command
{
    use UpMigrationsHelper;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->setDescription('Run all migrations that are not executed');
    }
}
