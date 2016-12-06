<?php

/*
 * This file is part of the Shepherd project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\AppBootstrapper;

use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;

/**
 * @package ActiveCollab\Shepherd\Utils
 */
class AppBootstrapper implements AppBootstrapperInterface
{
    /**
     * @var string
     */
    private $app_path = '';

    /**
     * @var array
     */
    private $app_settings;

    /**
     * @var bool
     */
    private $is_bootstrapped = false;

    /**
     * @var App
     */
    private $app;

    /**
     * @var bool
     */
    private $is_ran = false;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * AppBootstrapper constructor.
     *
     * @param string $app_path
     * @param array  $app_settings
     */
    public function __construct(string $app_path, array $app_settings = [])
    {
        $this->app_path = $app_path;
        $this->app_settings = $app_settings;
    }

    /**
     * {@inheritdoc}
     */
    public function getApp(): App
    {
        if (empty($this->app)) {
            throw new LogicException('App not set up.');
        }

        return $this->app;
    }

    /**
     * {@inheritdoc}
     */
    public function getAppPath(): string
    {
        return $this->app_path;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse(): ResponseInterface
    {
        if (empty($this->response)) {to
            throw new LogicException('Response not set up.');
        }

        return $this->response;
    }

    /**
     * {@inheritdoc}
     */
    public function isBootstrapped(): bool
    {
        return $this->is_bootstrapped;
    }

    /**
     * {@inheritdoc}
     */
    public function &bootstrap(): AppBootstrapperInterface
    {
        if ($this->isBootstrapped()) {
            throw new LogicException('App is already bootstrapped.');
        }

        $this->beforeAppConstruction();
        $this->app = new App($this->app_settings);
        $this->afterAppConstruction();

        $this->is_bootstrapped = true;

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

    /**
     * {@inheritdoc}
     */
    public function isRan(): bool
    {
        return $this->is_ran;
    }

    /**
     * {@inheritdoc}
     */
    public function &run(bool $silent = false): AppBootstrapperInterface
    {
        if (!$this->isBootstrapped()) {
            throw new LogicException('App needs to be bootstrapped before it can be ran.');
        }

        if ($this->isRan()) {
            throw new LogicException('App is already ran.');
        }

        $this->response = $this->app->run($silent);
        $this->is_ran = true;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function &logResponse(): AppBootstrapperInterface
    {
        if (!$this->isBootstrapped()) {
            throw new LogicException('App needs to be bootstrapped before it can be ran.');
        }

        if (!$this->isRan()) {
            throw new LogicException('App needs to be ran before response can be logged.');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!$this->isBootstrapped()) {
            throw new LogicException('App needs to be bootstrapped before it can be ran.');
        }

        return $this->app->process($request, $response);
    }
}
