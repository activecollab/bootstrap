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
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App as SlimApp;

class SlimAppBootstrapper extends AppBootstrapper implements WebAppBootstrapperInterface
{
    /**
     * @var SlimApp
     */
    private $app;

    private $response;

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
        $this->app = new SlimApp($this->getContainer());
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
