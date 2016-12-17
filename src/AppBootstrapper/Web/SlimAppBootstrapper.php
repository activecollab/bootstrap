<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\AppBootstrapper\Web;

use ActiveCollab\Bootstrap\AppBootstrapper\AppBootstrapper;
use ActiveCollab\Bootstrap\AppBootstrapper\AppBootstrapperInterface;
use ActiveCollab\Bootstrap\AppMetadata\AppMetadataInterface;
use ActiveCollab\Logger\LoggerInterface;
use Interop\Container\ContainerInterface;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App as SlimApp;

class SlimAppBootstrapper extends AppBootstrapper implements WebAppBootstrapperInterface
{
    private $container_or_settings;

    /**
     * @var SlimApp
     */
    private $app;

    private $response;

    public function __construct(AppMetadataInterface $app_metadata, $container_or_settings = [], LoggerInterface $logger = null)
    {
        parent::__construct($app_metadata, $logger);

        if (!is_array($container_or_settings) && !$container_or_settings instanceof ContainerInterface) {
            throw new LogicException('Container or array expected, ' . gettype($container_or_settings) . ' given');
        }

        $this->container_or_settings = $container_or_settings;
    }
    public function getApp(): SlimApp
    {
        if (empty($this->app)) {
            throw new LogicException('App not set up.');
        }

        return $this->app;
    }

    public function getResponse(): ResponseInterface
    {
        if (empty($this->response)) {
            throw new LogicException('Response not set up.');
        }

        return $this->response;
    }

    public function &bootstrap(): AppBootstrapperInterface
    {
        parent::bootstrap();

        $this->beforeAppConstruction();
        $this->app = new SlimApp($this->container_or_settings);
        $this->afterAppConstruction();

        $this->setIsBootstrapped();

        return $this;
    }

    public function &run(bool $silent = false): AppBootstrapperInterface
    {
        parent::run($silent);

        $this->response = $this->app->run($silent);
        $this->setIsRan();

        return $this;
    }

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

    public function process(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!$this->isBootstrapped()) {
            throw new LogicException('App needs to be bootstrapped before it can be ran.');
        }

        return $this->app->process($request, $response);
    }
}
