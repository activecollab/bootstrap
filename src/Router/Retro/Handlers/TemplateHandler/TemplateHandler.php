<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Router\Retro\Handlers\TemplateHandler;

use ActiveCollab\Bootstrap\Router\Retro\Handlers\Handler;
use ActiveCollab\TemplateEngine\TemplateEngineInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TemplateHandler extends Handler implements TemplateHandlerInterface
{
    private $templateEngine;
    private $templateName;
    private $templateAttributes;

    public function __construct(
        TemplateEngineInterface $templateEngine,
        string $templateName,
        array $templateAttributes = []
    )
    {
        $this->templateEngine = $templateEngine;
        $this->templateAttributes = $templateAttributes;
        $this->templateName = $templateName;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write(
            $this->templateEngine->fetch(
                $this->templateName,
                $this->templateAttributes
            )
        );

        return $response;
    }
}
