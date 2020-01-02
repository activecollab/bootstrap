<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\LoggerFactory;

use ActiveCollab\Bootstrap\App\Metadata\NameInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Psr\Log\LoggerInterface;

class LoggerFactory implements LoggerFactoryInterface
{
    private $appName;

    public function __construct(NameInterface $appName)
    {
        $this->appName = $appName;;
    }

    public function createLogger(HandlerInterface ...$handlers): LoggerInterface
    {
        $logger = new Logger($this->appName->getName());

        $formatter = new LineFormatter(
            "[%datetime%] %level_name%: %message% %context% %extra%\n",
            'Y-m-d H:i:s'
        );
        $processor = new PsrLogMessageProcessor();

        foreach ($handlers as $handler) {
            $handler->setFormatter($formatter);
            $handler->pushProcessor($processor);

            $logger->pushHandler($handler);
        }

        return $logger;
    }
}
