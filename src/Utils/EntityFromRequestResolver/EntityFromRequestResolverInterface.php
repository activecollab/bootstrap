<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Bootstrap\Utils\EntityFromRequestResolver;

use ActiveCollab\DatabaseObject\Entity\EntityInterface;
use Psr\Http\Message\ServerRequestInterface;

interface EntityFromRequestResolverInterface
{
    public function getEntityFromRequest(
        ServerRequestInterface $request,
        string $entity_type,
        string $param_name,
        bool $get_param_from_body = false
    ): ?EntityInterface;

    public function mustGetEntityFromRequest(
        ServerRequestInterface $request,
        string $entity_type,
        string $param_name,
        bool $get_param_from_body = false
    ): EntityInterface;
}
