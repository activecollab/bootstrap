<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Bootstrap\Command\DevCommand\Migrations;

use ActiveCollab\DatabaseMigrations\Command\Create as CreateMigrationsHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * @package ActiveCollab\Bootstrap\Command\DevCommand\Migrations
 */
class Create extends Command
{
    use CreateMigrationsHelper;

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        parent::configure();

        $this->setDescription('Create a new migration')
            ->addArgument('migration_name', InputArgument::REQUIRED, 'What is this migration supposed to do (use imperative voice)')
            ->addOption('changeset', '', InputOption::VALUE_REQUIRED, 'Changeset name')
            ->addOption('dry-run', '', InputOption::VALUE_NONE, "Example what you'll do, without creating an actual file");
    }

    /**
     * @return string
     */
    protected function getHeaderComment()
    {
        return "This file is part of the Active Collab ID project.\n\n(c) A51 doo <info@activecollab.com>. All rights reserved.";
    }

    /**
     * @return string
     */
    protected function getNamespace()
    {
        return 'ActiveCollab\Id\Model\Migrations';
    }

    /**
     * @param  InputInterface $input
     * @return string
     */
    public function getMigrationName(InputInterface $input)
    {
        return trim($input->getArgument('migration_name'));
    }

    /**
     * @param  InputInterface $input
     * @return array
     */
    protected function getExtraArguments(InputInterface $input)
    {
        $result = [];

        if (!empty($input->getOption('changeset'))) {
            $result[] = $input->getOption('changeset');
        }

        return $result;
    }

    /**
     * @param  InputInterface $input
     * @return bool
     */
    protected function isDryRun(InputInterface $input)
    {
        return (bool) $input->getOption('dry-run');
    }
}
