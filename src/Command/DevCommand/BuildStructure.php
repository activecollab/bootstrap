<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Bootstrap\Command;

use ActiveCollab\Bootstrap\Command\DevCommand\DevCommand;
use ActiveCollab\DatabaseConnection\ConnectionInterface;
use ActiveCollab\DatabaseStructure\StructureInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @package ActiveCollab\Id\Command
 */
class BuildStructure extends DevCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->setDescription('Build PHP classes and database structure from model definition');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ConnectionInterface $connection */
        $connection = $this->getContainer()->get('connection');

        /** @var StructureInterface $structure */
        $structure = $this->getContainer()->get('structure');

        $structure->build($this->getContainer()->get('app_root') . '/app/src/Model', $connection, [
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
        ]);

        $output->writeln('');

        $this->getContainer()->get('migrations')->setAllAsExecuted();

        foreach ($this->getContainer()->get('migrations')->getMigrations() as $migration) {
            $output->writeln('<info>OK</info>: Migration <comment>' . get_class($migration) . '</comment> is marked as executed');
        }
    }
}
