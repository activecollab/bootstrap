<?php

/*
 * This file is part of the Slingshot project.
 *
 * (c) PhpCloud.org Core Team <core@phpcloud.org>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Router\Retro\RoutingContext;

interface RoutingContextInterface
{
    public function getUrl(string $subpageName = null): string;
    public function getRoutePrefix(): string;
    public function getRouteData(): array;
}
