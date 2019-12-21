<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\App\Metadata;

interface EnvironmentInterface
{
    const PRODUCTION = 'production';
    const DEVELOPMENT = 'development';
    const TEST = 'test';

    const VALID_ENVIRONMENTS = [
        self::PRODUCTION,
        self::DEVELOPMENT,
        self::TEST,
    ];

    public function getEnvironment(): string;

    public function isProduction(): bool;
    public function isDevelopment(): bool;
    public function isTest(): bool;
}
