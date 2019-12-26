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
use ActiveCollab\Bootstrap\Router\Retro\Linker\LinkerInterface;
use ActiveCollab\Bootstrap\Router\Retro\Linker\SlimAppLinker;
use DI\Bridge\Slim\Bridge;
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

    public function getApp(): SlimApp
    {
        if (empty($this->app)) {
            throw new LogicException('App not set up.');
        }

        return $this->app;
    }

    public function getLinker(): LinkerInterface
    {
        if (!$this->isBootstrapped()) {
            throw new LogicException("Linker can't be prepared if app is not bootstrapped.");
        }

        return new SlimAppLinker($this->getApp()->getRouteCollector()->getRouteParser());
    }

    public function bootstrap(): AppBootstrapperInterface
    {
        parent::bootstrap();

        $this->beforeAppConstruction();

        $this->app = Bridge::create($this->getContainer());
        $this->app->addRoutingMiddleware();

        $this->afterAppConstruction();

        $this->setIsBootstrapped();

        return $this;
    }

    public function run(bool $silent = false): AppBootstrapperInterface
    {
        parent::run($silent);

        $this->app->run();
        $this->setIsRan();

        return $this;
    }

    public function handle(
        ServerRequestInterface $request
    ): ResponseInterface
    {
        if (!$this->isBootstrapped()) {
            throw new LogicException('App needs to be bootstrapped before it can be ran.');
        }

        return $this->app->handle($request);
    }
}
