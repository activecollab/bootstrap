<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Router\Retro\Linker;

use Slim\Interfaces\RouteParserInterface;

class SlimAppLinker implements LinkerInterface
{
    private $routeParser;

    public function __construct(RouteParserInterface $routeParser)
    {
        $this->routeParser = $routeParser;
    }

    public function link(
        string $routeName,
        array $data = [],
        array $queryParams = []
    ): string
    {
        return $this->routeParser->urlFor(
            $routeName,
            $data,
            $queryParams
        );
    }
}
