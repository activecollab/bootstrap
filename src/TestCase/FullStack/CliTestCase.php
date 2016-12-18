<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\TestCase\FullStack;

use ActiveCollab\Bootstrap\AppBootstrapper\Cli\CliAppBootstrapperInterface;
use Symfony\Component\Console\Tester\CommandTester;

abstract class CliTestCase extends TestCase
{
    protected function executeCommand(string $command, array $command_arguments = []): CommandTester
    {
        $command_tester = $this->getCommandTesterFor($command);
        $command_tester->execute(array_merge(['command' => $command], $command_arguments));

        return $command_tester;
    }

    protected function getCommandTesterFor(string $command): CommandTester
    {
        /** @var CliAppBootstrapperInterface $app_boostrapper */
        $app_boostrapper = $this->getAppBootstrapper();

        if (!$app_boostrapper->isBootstrapped()) {
            $app_boostrapper->bootstrap();
        }

        return new CommandTester($app_boostrapper->getCommand($command));
    }
}
