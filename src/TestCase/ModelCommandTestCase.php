<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare (strict_types = 1);

namespace ActiveCollab\Bootstrap\TestCase;

use ActiveCollab\Bootstrap\Command\Command;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @package ActiveCollab\Id\Test\Base
 */
abstract class ModelCommandTestCase extends ModelTestCase
{
    /**
     * @var Application
     */
    private $application;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->application = new Application();
    }

    /**
     * @param  string        $command_class
     * @return CommandTester
     */
    protected function getCommandTesterFor($command_class): CommandTester
    {
        /** @var Command $command */
        $command = new $command_class();
        $command->setContainer($this->getContainer());

        $this->application->add($command);

        return new CommandTester($this->application->find($command->getName()));
    }

    /**
     * @param  string        $command_class
     * @param  string        $command
     * @param  array         $command_arguments
     * @return CommandTester
     */
    protected function executeCommand($command_class, $command, array $command_arguments = []): CommandTester
    {
        $command_tester = $this->getCommandTesterFor($command_class);
        $command_tester->execute(array_merge(['command' => $command], $command_arguments));

        return $command_tester;
    }
}
