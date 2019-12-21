<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Router;

use ActiveCollab\Bootstrap\App\Metadata\EnvironmentInterface;
use ActiveCollab\Bootstrap\App\Metadata\PathInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class LoggerFactory implements LoggerFactoryInterface
{
    private $environment;
    private $path;

    public function __construct(
        EnvironmentInterface $environment,
        PathInterface $path
    )
    {
        $this->environment = $environment;
        $this->path = $path;
    }

    public function createLogger(): LoggerInterface
    {
        $logger = new Logger('name');

        $logger->pushHandler(
            new StreamHandler(
                $this->path->getPath() . '/logs/log.txt',
                Logger::DEBUG
            )
        );

        return $logger;
    }
}
