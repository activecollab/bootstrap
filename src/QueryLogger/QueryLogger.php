<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\QueryLogger;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class QueryLogger implements QueryLoggerInterface
{
    private $logger;
    private $logLevel;
    private $queries = [];
    private $executionTime = 0.0;

    public function __construct(LoggerInterface $logger, string $logLevel = LogLevel::DEBUG)
    {
        $this->logger = $logger;
        $this->logLevel = $logLevel;
    }

    public function __invoke(string $querySql, float $queryExecutionTime)
    {
        $this->logger->log(
            $this->logLevel,
            'Query {query} ran in {time}s.',
            [
                'query' => $querySql,
                'time' => round($queryExecutionTime, 5)
            ]
        );

        $this->queries[] = $querySql;
        $this->executionTime += $queryExecutionTime;
    }

    public function getNumberOfQueries(): int
    {
        return count($this->queries);
    }

    public function getExecutionTime(): float
    {
        return round($this->executionTime, 5);
    }
}
