<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Command\DevCommand\Migrations;

use ActiveCollab\Bootstrap\MigrationsHeaderCommentResolver\MigrationsHeaderCommentResolverInterface;
use ActiveCollab\Bootstrap\MigrationsNamespaceResolver\MigrationsNamespaceResolverInterface;
use ActiveCollab\DatabaseMigrations\Command\Create as CreateMigrationsHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class Create extends Command
{
    use CreateMigrationsHelper;

    public function configure()
    {
        parent::configure();

        $this
            ->setDescription('Create a new migration')
            ->addArgument(
                'migration_name',
                InputArgument::REQUIRED,
                'What is this migration supposed to do (use imperative voice)'
            )
            ->addOption(
                'changeset',
                '',
                InputOption::VALUE_REQUIRED, 'Changeset name'
            )
            ->addOption(
                'dry-run',
                '',
                InputOption::VALUE_NONE,
                "Example what you'll do, without creating an actual file"
            );
    }

    protected function getHeaderComment(): string
    {
        return $this->getContainer()
            ->get(MigrationsHeaderCommentResolverInterface::class)
                ->getMigrationsHeaderComment();
    }

    protected function getNamespace(): string
    {
        return $this->getContainer()
            ->get(MigrationsNamespaceResolverInterface::class)
                ->getMigrationsNamespace();
    }

    public function getMigrationName(InputInterface $input): string
    {
        return trim($input->getArgument('migration_name'));
    }

    protected function getExtraArguments(InputInterface $input): array
    {
        $result = [];

        if (!empty($input->getOption('changeset'))) {
            $result[] = $input->getOption('changeset');
        }

        return $result;
    }

    protected function isDryRun(InputInterface $input): bool
    {
        return (bool) $input->getOption('dry-run');
    }
}
