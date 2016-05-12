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

/**
 * @package ActiveCollab\Bootstrap\Command\DevCommand\Migrations
 */
abstract class Command extends DevCommand
{
    /**
     * {@inheritdoc}
     */
    public function getCommandNamePrefix()
    {
        return parent::getCommandNamePrefix() . 'migrations:';
    }

    /**
     * Return migrations instance.
     *
     * @return MigrationsInterface
     */
    public function &getMigrations()
    {
        $migrations = $this->getContainer()->get('migrations');

        if ($migrations instanceof MigrationsInterface) {
            return $migrations;
        } else {
            throw new RuntimeException('Failed to get migrations utility from DI container');
        }
    }
}
