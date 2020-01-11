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
use ActiveCollab\Bootstrap\Router\Retro\Sitemap\SitemapInterface;
use ActiveCollab\Bootstrap\SitemapPathResolver\SitemapPathResolverInterface;
use Slim\Interfaces\RouteInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SitemapCommand extends DevCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setDescription('Output how sitemap is built and routed.');
    }


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

        /** @var SitemapInterface $sitemapLoader */
        $sitemapLoader = $this->getContainer()->get(SitemapInterface::class);

        if (!$sitemapLoader->isLoaded()) {
            $this->getContainer()
                ->get(WebAppBootstrapperInterface::class)
                    ->bootstrap();
        }

        /** @var RouteInterface $route */
        foreach ($sitemapLoader->getLoadedRoutes() as $route) {
            $table->addRow(
                [
                    $this->renderPattern($route->getPattern()),
                    $this->renderName($route->getName()),
                ]
            );
        }

        $table->render();
    }

    protected function getSitemapPath(): string
    {
        return $this->getContainer()->get(SitemapPathResolverInterface::class)->getSitemapPath();
    }

    private function renderPattern(string $routePattern): string
    {
        return $routePattern;
    }

    public function renderName(string $routeName): string
    {
        return $routeName;
    }
}
