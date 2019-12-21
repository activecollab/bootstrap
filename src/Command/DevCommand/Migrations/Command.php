<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Bootstrap\Command\DevCommand\Migrations;

use ActiveCollab\Bootstrap\Command\DevCommand\DevCommand;
use ActiveCollab\DatabaseMigrations\MigrationsInterface;
use RuntimeException;

abstract class Command extends DevCommand
{
    public function getCommandNamePrefix(): string
    {
        return parent::getCommandNamePrefix() . 'migrations:';
    }

    public function getMigrations(): MigrationsInterface
    {
        $migrations = $this->getContainer()->get(MigrationsInterface::class);

        if ($migrations instanceof MigrationsInterface) {
            return $migrations;
        } else {
            throw new RuntimeException('Failed to get migrations utility from DI container');
        }
    }
}
