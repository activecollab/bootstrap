<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Router;

interface RouterInterface
{
    public function map(
        $path,
        $controller_name,
        array $method_to_action = [],
        $name = ''
    );

    public function mapModel(
        $model_class,
        array $settings = null,
        callable $extend_collection = null,
        callable $extend_single = null
    );
}
