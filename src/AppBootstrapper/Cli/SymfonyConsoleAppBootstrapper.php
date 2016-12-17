<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\AppBootstrapper\Cli;

use ActiveCollab\Bootstrap\AppBootstrapper\AppBootstrapper;
use ActiveCollab\Bootstrap\AppBootstrapper\AppBootstrapperInterface;
use ActiveCollab\Bootstrap\ClassFinder\ClassDir\ClassDir;
use ActiveCollab\Bootstrap\ClassFinder\ClassDir\ClassDirInterface;
use ActiveCollab\Bootstrap\ClassFinder\ClassFinder;
use ActiveCollab\Bootstrap\Command\CommandInterface;
use ActiveCollab\ContainerAccess\ContainerAccessInterface;
use Interop\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

class SymfonyConsoleAppBootstrapper extends AppBootstrapper implements CliAppBootstrapperInterface
{
    /**
     * @var Application
     */
    private $app;

    private $exit_code = 0;

    public function &bootstrap(): AppBootstrapperInterface
    {
        parent::bootstrap();

        $this->beforeAppConstruction();

        $this->app = new Application($this->getAppMetadata()->getName(), $this->getAppMetadata()->getVersion());
        $this->container = '';
        $this->scanDirsForCommands($this->app, $this->container);

        $this->afterAppConstruction();

        $this->setIsBootstrapped();

        return $this;
    }

    public function &run(bool $silent = false): AppBootstrapperInterface
    {
        parent::run($silent);

        $this->exit_code = $this->app->run();
        $this->setIsRan();

        return $this;
    }

    protected function scanDirsForCommands(Application $app, ContainerInterface $container)
    {
        (new ClassFinder())->scanDirsForInstances($this->getDirsToScan(), function (Command $command) use (&$app, &$container) {
            if ($command instanceof ContainerAccessInterface) {
                $command->setContainer($container);
            }

            $app->add($command);
        });
    }

    /**
     * @return ClassDirInterface[]
     */
    protected function getDirsToScan()
    {
        return [
            new ClassDir(dirname(__DIR__, 2) . '/Command', '\ActiveCollab\Bootstrap\Command', CommandInterface::class),
        ];
    }
}
