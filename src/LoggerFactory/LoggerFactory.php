<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\LoggerFactory;

use ActiveCollab\Bootstrap\App\Metadata\EnvironmentInterface;
use ActiveCollab\Bootstrap\App\Metadata\PathInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
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

        $handler = new RotatingFileHandler(
            $this->path->getPath() . '/logs/log.txt',
            7,
            $this->environment->isTest()
                ? Logger::DEBUG
                : Logger::INFO
        );

        $handler->setFormatter(
            new LineFormatter(
                "[%datetime%] %level_name%: %message% %context% %extra%\n",
                'Y-m-d H:i:s'
            )
        );
        $handler->pushProcessor(new PsrLogMessageProcessor());

        $logger->pushHandler($handler);

        return $logger;
    }
}
