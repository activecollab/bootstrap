<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Router\RouteExtender;

interface RouteExtenderInterface
{
    public function extend($path, array $method_to_action = [], $name = '', $controller_name = null);
}
