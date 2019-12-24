<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Command\DevCommand;

use ActiveCollab\Bootstrap\Router\Retro\Nodes\Directory\DirectoryInterface;
use ActiveCollab\Bootstrap\Router\Retro\Nodes\NodeInterface;
use ActiveCollab\Bootstrap\Router\Retro\Router;
use ActiveCollab\Bootstrap\SitemapPathResolver\SitemapPathResolverInterface;
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
                'Type',
                'Route',
            ]
        );

        $this->recursivelyPopulateRows($root, '', $table);

        $table->render();
    }

    private function recursivelyPopulateRows(DirectoryInterface $directory, string $indent, Table $table): void
    {
        $table->addRow(
            [
                $this->getDirectoryStructureContent($directory, $indent),
                $directory->isSystem() ? '<info>system dir</info>' : 'dir',
                $this->getNodeRoute($directory),
            ]
        );

        foreach ($directory->getSubdirectories() as $subdirectory) {
            $this->recursivelyPopulateRows($subdirectory, $this->increaseIndent($indent), $table);
        }

        foreach ($directory->getFiles() as $file) {
            $table->addRow(
                [
                    $this->getNodePath($file, $this->increaseIndent($indent)),
                    $file->isSystem() ? '<info>system file</info>' : 'file',
                    $this->getNodeRoute($file),
                ]
            );
        }
    }

    private function getDirectoryStructureContent(DirectoryInterface $directory, string $indent): string
    {
        $result = $this->getNodePath($directory, $indent);

        if ($directory->hasIndex()) {
            $result .= sprintf(' ~ %s', $directory->getIndex()->getNodeName());
        }

        return $result;
    }

    private function getNodePath(NodeInterface $node, string $indent): string
    {
        return $indent . '/' . $node->getBasename();
    }

    private function getNodeRoute(NodeInterface $node): string
    {
        return $node->getRoute() ? '/' . $node->getRoute()->getFullPath() : '--';
    }

    private function increaseIndent(string $indent): string
    {
        return $indent . '    ';
    }

    protected function getSitemapPath(): string
    {
        return $this->getContainer()->get(SitemapPathResolverInterface::class)->getSitemapPath();
    }
}
