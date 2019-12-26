<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Router\Retro\Pathfinder\NodeHandlerDetector;

use ActiveCollab\Bootstrap\Router\Retro\Handlers\HandlerInterface;
use ActiveCollab\Bootstrap\Router\Retro\Handlers\TemplateHandler\TemplateHandler;
use ActiveCollab\Bootstrap\Router\Retro\Nodes\File\FileInterface;
use ActiveCollab\Bootstrap\Router\Retro\Nodes\NodeInterface;
use ActiveCollab\TemplateEngine\TemplateEngineInterface;

class TemplateNodeDetector implements NodeHandlerDetectorInterface
{
    private $templateEngine;
    private $templateExtensions;

    public function __construct(
        TemplateEngineInterface $templateEngine,
        array $templateExtensions = [
            'twig',
            'tpl',
        ]
    )
    {
        $this->templateEngine = $templateEngine;
        $this->templateExtensions = $templateExtensions;
    }

    public function probe(NodeInterface $node): ?HandlerInterface
    {
        if ($node instanceof FileInterface && $this->hasTemplateExtension($node)) {
            return new TemplateHandler(
                $this->templateEngine,
                $node->getNodePath(),
            );
        }

        return null;
    }

    private function hasTemplateExtension(FileInterface $fileNode): bool
    {
        return in_array($fileNode->getExtension(), $this->templateExtensions);
    }
}
