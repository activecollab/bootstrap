<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Command;

use ActiveCollab\Bootstrap\Router\Retro\Nodes\DirectoryInterface;
use ActiveCollab\Bootstrap\Router\Retro\Router;
use ActiveCollab\Bootstrap\SitemapPathResolver\SitemapPathResolverInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SitemapCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $root  = (new Router())->scan($this->getSitemapPath());

        $table = new Table($output);
        $table->setHeaders(
            [
                'Path',
                'Type',
            ]
        );

        $this->recursivelyPopulateRows($root, '', $table);

        $table->render();
    }

    private function recursivelyPopulateRows(DirectoryInterface $directory, string $indent, Table $table): void
    {
        $table->addRow(
            [
                $indent . $directory->getNodePath(),
                'dir',
            ]
        );

        foreach ($directory->getSubdirectories() as $subdirectory) {
            $this->recursivelyPopulateRows($subdirectory, $indent . '  ', $table);
        }

        foreach ($directory->getFiles() as $file) {
            $table->addRow(
                [
                    $indent . $file->getNodePath(),
                    'file',
                ]
            );
        }
    }

    protected function getSitemapPath(): string
    {
        return $this->getContainer()->get(SitemapPathResolverInterface::class)->getSitemapPath();
    }
}
