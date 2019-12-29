<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Command\DevCommand;

use ActiveCollab\Bootstrap\App\Metadata\PathInterface;
use ActiveCollab\DatabaseConnection\ConnectionInterface;
use ActiveCollab\DatabaseMigrations\MigrationsInterface;
use ActiveCollab\DatabaseStructure\StructureInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildStructureCommand extends DevCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setDescription(
            'Build PHP classes and database structure from model definition'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->getConnection();
        $structure = $this->getStructure();
        $migrations = $this->getMigrations();

        $modelPath = $this->getSystemPath()->getPath() . '/app/current/src/Model';

        $structure->build(
            $modelPath,\
            $connection,
                [
                'on_dir_created' => function ($base_dir_path) use ($output) {
                    $output->writeln("<info>OK</info>: Directory '$base_dir_path' created");
                },
                'on_class_built' => function ($class_name, $class_build_path) use ($output) {
                    $output->writeln("<info>OK</info>: Class $class_name built at $class_build_path");
                },
                'on_class_build_skipped' => function ($class_name, $class_build_path) use ($output) {
                    $output->writeln("<comment>Notice</comment>: Skipping $class_name because file $class_build_path already exists");
                },
                'on_types_built' => function ($types_build_path) use ($output) {
                    $output->writeln("<info>OK</info>: File '$types_build_path' created");
                },
                'on_structure_sql_built' => function ($structure_sql_build_path) use ($output) {
                    $output->writeln("<info>OK</info>: File '$structure_sql_build_path' created");
                },
                'on_table_exists' => function ($table_name) use ($output) {
                    $output->writeln("<comment>Notice</comment>: Table $table_name already exists");
                },
                'on_table_created' => function ($table_name) use ($output) {
                    $output->writeln("<info>OK</info>: Table $table_name created");
                },
                'on_association_exists' => function ($association_description) use ($output) {
                    $output->writeln("<comment>Notice</comment>: Association $association_description already exists");
                },
                'on_association_created' => function ($association_description) use ($output) {
                    $output->writeln("<info>OK</info>: Association $association_description created");
                },
                'on_trigger_exists' => function ($trigger_name) use ($output) {
                    $output->writeln("<comment>Info</comment>: Trigger $trigger_name already exists");
                },
                'on_trigger_created' => function ($trigger_name) use ($output) {
                    $output->writeln("<info>OK</info>: Trigger $trigger_name created");
                },
            ]
        );

        $output->writeln('');

        $migrations->setAllAsExecuted();

        foreach ($migrations as $migration) {
            $output->writeln(
                sprintf(
                    '<info>OK</info>: Migration <comment>%s</comment> is marked as executed',
                    get_class($migration)
                )
            );
        }
    }

    private function getConnection(): ConnectionInterface
    {
        return $this->getContainer()->get(ConnectionInterface::class);
    }

    public function getStructure(): StructureInterface
    {
        return $this->getContainer()->get(StructureInterface::class);
    }

    public function getMigrations(): MigrationsInterface
    {
        return $this->getContainer()->get(MigrationsInterface::class);
    }

    public function getSystemPath(): PathInterface
    {
        return $this->getContainer()->get(PathInterface::class);
    }
}
