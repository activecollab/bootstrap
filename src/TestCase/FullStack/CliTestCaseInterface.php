<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\TestCase\FullStack;

use Symfony\Component\Console\Tester\CommandTester;

interface CliTestCaseInterface
{
    public function executeCommand(string $command, array $command_arguments = []): CommandTester;

    public function getCommandTesterFor(string $command): CommandTester;
}
