<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\AppBootstrapper;

use ActiveCollab\Bootstrap\App\Metadata\MetadataInterface;
use LogicException;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

abstract class AppBootstrapper implements AppBootstrapperInterface
{
    private $app_metadata;
    private $container;
    private $logger;
    private $is_bootstrapped = false;
    private $is_ran = false;

    public function __construct(
        MetadataInterface $app_metadata,
        ContainerInterface $container,
        LoggerInterface $logger
    )
    {
        $this->app_metadata = $app_metadata;
        $this->container = $container;
        $this->logger = $logger;
    }

    public function getAppMetadata(): MetadataInterface
    {
        return $this->app_metadata;
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function isBootstrapped(): bool
    {
        return $this->is_bootstrapped;
    }

    protected function setIsBootstrapped(): void
    {
        $this->is_bootstrapped = true;
    }

    public function isRan(): bool
    {
        return $this->is_ran;
    }

    public function bootstrap(): AppBootstrapperInterface
    {
        if ($this->isBootstrapped()) {
            throw new LogicException('App is already bootstrapped.');
        }

        $this->logger->debug(sprintf('%s bootstrapped.', get_class($this)));

        return $this;
    }

    /**
     * Ran before app instance is constructed in boostrap method.
     */
    protected function beforeAppConstruction()
    {
    }

    /**
     * Ran after app instance is constructed in boostrap method.
     */
    protected function afterAppConstruction()
    {
    }

    public function run(bool $silent = false): AppBootstrapperInterface
    {
        if (!$this->isBootstrapped()) {
            throw new LogicException('App needs to be bootstrapped before it can be ran.');
        }

        if ($this->isRan()) {
            throw new LogicException('App is already ran.');
        }

        $this->logger->debug(sprintf('%s ran.', get_class($this)));

        return $this;
    }
}
