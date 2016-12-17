<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\TestCase\FullStack;

use ActiveCollab\Bootstrap\AppBootstrapper\Cli\CliAppBootstrapperInterface;
use ActiveCollab\Bootstrap\Command\CommandInterface;
use Symfony\Component\Console\Tester\CommandTester;

abstract class CliTestCase extends FullStackTestCase
{
    /**
     * @param  string        $command_class
     * @return CommandTester
     */
    protected function getCommandTesterFor($command_class): CommandTester
    {
        /** @var CliAppBootstrapperInterface $app_boostrapper */
        $app_boostrapper = $this->getAppBootstrapper();

        if (!$app_boostrapper->isBootstrapped()) {
            $app_boostrapper->bootstrap();
        }

        /** @var CommandInterface $command */
        $command = new $command_class();
        $command->setContainer($app_boostrapper->getContainer());

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
