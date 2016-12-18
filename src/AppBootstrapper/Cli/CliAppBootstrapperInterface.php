<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\AppBootstrapper\Cli;

use ActiveCollab\Bootstrap\AppBootstrapper\AppBootstrapperInterface;
use ActiveCollab\Bootstrap\Command\CommandInterface;

interface CliAppBootstrapperInterface extends AppBootstrapperInterface
{
    public function getCommand(string $command): CommandInterface;

    public function &addCommand(CommandInterface $command): CliAppBootstrapperInterface;
}
