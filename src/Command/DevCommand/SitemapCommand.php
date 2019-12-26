<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Command\DevCommand;

use ActiveCollab\Bootstrap\AppBootstrapper\Web\WebAppBootstrapperInterface;
use ActiveCollab\Bootstrap\Router\Retro\Router;
use ActiveCollab\Bootstrap\Router\Retro\SitemapLoader\SitemapLoaderInterface;
use ActiveCollab\Bootstrap\SitemapPathResolver\SitemapPathResolverInterface;
use Slim\Interfaces\RouteInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SitemapCommand extends DevCommand
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $root  = (new Router())->scan($this->getSitemapPath());

        $table = new Table($output);
        $table->setHeaders(
            [
                'Structure',
                'Route',
            ]
        );

        /** @var SitemapLoaderInterface $sitemapLoader */
        $sitemapLoader = $this->getContainer()
            ->get(SitemapLoaderInterface::class)
                ->getLoadedRoutes();

        if (!$sitemapLoader->isLoaded()) {
            $this->getContainer()
                ->get(WebAppBootstrapperInterface::class)
                    ->bootstrap();
        }

        /** @var RouteInterface $route */
        foreach ($this->getContainer()->get(SitemapLoaderInterface::class)->getLoadedRoutes() as $route) {
            $table->addRow(
                [
                    $route->getPattern(),
                    $route->getPattern(),
                ]
            );
        }

        $table->render();
    }

    protected function getSitemapPath(): string
    {
        return $this->getContainer()->get(SitemapPathResolverInterface::class)->getSitemapPath();
    }
}
