<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\QueryLogger;

class QueryLogger implements QueryLoggerInterface
{
    private $number_of_queries = 0;
    private $execution_time = 0.0;

    public function __invoke(string $query_sql, float $query_execution_time)
    {
        $this->number_of_queries++;
        $this->execution_time += $query_execution_time;
    }

    public function getNumberOfQueries(): int
    {
        return $this->number_of_queries;
    }

    public function getExecutionTime(): float
    {
        return round($this->execution_time, 5);
    }
}
