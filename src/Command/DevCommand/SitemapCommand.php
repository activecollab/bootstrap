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
use ActiveCollab\Bootstrap\Router\Retro\Pathfinder\PathfinderInterface;
use ActiveCollab\Bootstrap\Router\Retro\Router;
use ActiveCollab\Bootstrap\SitemapPathResolver\SitemapPathResolverInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SitemapCommand extends DevCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->addOption(
                'include-system',
                '',
                InputOption::VALUE_NONE,
                'Include system directories in the structure'
            )
            ->addOption(
                'include-indexes',
                '',
                InputOption::VALUE_NONE,
                'Include index files in the structure, as lines'
            );
    }

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

        $this->recursivelyPopulateRows($root, '', $table, $input);

        $table->render();
    }

    private function recursivelyPopulateRows(
        DirectoryInterface $directory,
        string $indent,
        Table $table,
        InputInterface $input
    ): void
    {
        if ($directory->isSystem() && empty($input->getOption('include-system'))) {
            return;
        }

        $table->addRow(
            [
                $this->getDirectoryStructureContent($directory, $indent),
                $directory->isSystem() ? '<info>system dir</info>' : 'dir',
                $this->getNodeRoute($directory),
            ]
        );

        foreach ($directory->getSubdirectories() as $subdirectory) {
            $this->recursivelyPopulateRows($subdirectory, $this->increaseIndent($indent), $table, $input);
        }

        foreach ($directory->getFiles() as $file) {
            if ($file->isSystem() && empty($input->getOption('include-system'))) {
                continue;
            }

            if ($file->isIndex() && empty($input->getOption('include-indexes'))) {
                continue;
            }

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
            $result .= sprintf(' <comment>~ %s</comment>', $directory->getIndex()->getBasename());
        }

        return $result;
    }

    private function getNodePath(NodeInterface $node, string $indent): string
    {
        return $indent . '/' . $node->getBasename();
    }

    private function getNodeRoute(NodeInterface $node): string
    {
        $pathfinder = $this->getContainer()->get(PathfinderInterface::class);

        return $pathfinder->hasRoute($node)
            ? '/' . $pathfinder->getRoutingPath($node)
            : '--';
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
