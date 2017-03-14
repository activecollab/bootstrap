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
    const PARAM_SOURCE_ROUTE_ARGUMENT = 'route_argument';
    const PARAM_SOURCE_QUERY_STRING = 'query_string';
    const PARAM_SOURCE_BODY_PARAM = 'body_param';

    const PARAM_SOURCES = [
        self::PARAM_SOURCE_ROUTE_ARGUMENT,
        self::PARAM_SOURCE_QUERY_STRING,
        self::PARAM_SOURCE_BODY_PARAM,
    ];

    public function getEntityFromRequest(
        ServerRequestInterface $request,
        string $entity_type,
        string $param_name,
        string $param_source = self::PARAM_SOURCE_ROUTE_ARGUMENT
    ): ?EntityInterface;

    public function mustGetEntityFromRequest(
        ServerRequestInterface $request,
        string $entity_type,
        string $param_name,
        string $param_source = self::PARAM_SOURCE_ROUTE_ARGUMENT
    ): EntityInterface;
}
