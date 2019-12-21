<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\AppBootstrapper;

use ActiveCollab\Bootstrap\AppMetadata\AppMetadataInterface;
use LogicException;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

abstract class AppBootstrapper implements AppBootstrapperInterface
{
    private $logger;

    private $app_metadata;

    private $container;

    private $is_bootstrapped = false;

    private $is_ran = false;

    public function __construct(
        AppMetadataInterface $app_metadata,
        ContainerInterface $container,
        LoggerInterface $logger = null
    )
    {
        $this->setAppMetadata($app_metadata);
        $this->setContainer($container);
        $this->setLogger($logger);
    }

    public function getAppMetadata(): AppMetadataInterface
    {
        return $this->app_metadata;
    }

    protected function setAppMetadata(AppMetadataInterface $app_metadata): AppBootstrapperInterface
    {
        $this->app_metadata = $app_metadata;

        return $this;
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function setContainer(ContainerInterface $container): AppBootstrapperInterface
    {
        $this->container = $container;

        return $this;
    }

    public function isBootstrapped(): bool
    {
        return $this->is_bootstrapped;
    }

    public function setIsBootstrapped(bool $is_bootstrapped = true): AppBootstrapperInterface
    {
        $this->is_bootstrapped = $is_bootstrapped;

        return $this;
    }

    public function isRan(): bool
    {
        return $this->is_ran;
    }

    protected function setIsRan(bool $is_ran = true): AppBootstrapperInterface
    {
        $this->is_ran = $is_ran;

        return $this;
    }

    public function bootstrap(): AppBootstrapperInterface
    {
        if ($this->isBootstrapped()) {
            throw new LogicException('App is already bootstrapped.');
        }

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

        return $this;
    }

    protected function getLogger(): ? LoggerInterface
    {
        return $this->logger;
    }

    protected function setLogger(LoggerInterface $logger = null): AppBootstrapperInterface
    {
        $this->logger = $logger;

        return $this;
    }
}
