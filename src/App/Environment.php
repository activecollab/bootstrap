<?php

/*
 * This file is part of the Shepherd project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\App;

use ActiveCollab\Bootstrap\App\EnvironmentInterface;
use InvalidArgumentException;
use LogicException;

class Environment implements EnvironmentInterface
{
    private $environment;

    public function __construct(string $environment)
    {
        if (!$this->isValidEnvironment($environment)) {
            throw new InvalidArgumentException("Value '{$environment}' is not a supported environment.");
        }

        $this->environment = $environment;
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    private function isValidEnvironment(string $environment): bool
    {
        return in_array($environment, self::VALID_ENVIRONMENTS);
    }

    public function isProduction(): bool
    {
        return $this->environment === self::PRODUCTION;
    }

    public function isDevelopment(): bool
    {
        return $this->environment === self::DEVELOPMENT;
    }

    public function isTest(): bool
    {
        return $this->environment === self::TEST;
    }

    public function __toString()
    {
        return $this->getEnvironment();
    }
}
