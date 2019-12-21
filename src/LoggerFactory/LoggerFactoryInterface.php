<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\LoggerFactory;

use Psr\Log\LoggerInterface;

interface LoggerFactoryInterface
{
    public function createLogger(): LoggerInterface;
}
