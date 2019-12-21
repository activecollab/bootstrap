<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Command\DevCommand\Migrations;

use ActiveCollab\DatabaseMigrations\Command\All as AllMigrationsHelper;

class All extends Command
{
    use AllMigrationsHelper;

    protected function configure()
    {
        parent::configure();

        $this->setDescription('List all migrations');
    }
}
