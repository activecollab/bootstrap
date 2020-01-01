<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Router\Retro\RequestHandler;

use ActiveCollab\Bootstrap\Router\Retro\Sitemap\SitemapInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestHandler implements MiddlewareInterface
{
    private $responseFactory;
    private $sitemap;

    public function __construct(ResponseFactoryInterface $responseFactory, SitemapInterface $sitemap)
    {
        $this->responseFactory = $responseFactory;
        $this->sitemap = $sitemap;

        $this->configure();
    }

    protected function configure(): void
    {
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface
    {
    }
}
