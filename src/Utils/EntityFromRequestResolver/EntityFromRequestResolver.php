<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Utils\EntityFromRequestResolver;

use ActiveCollab\Bootstrap\Exception\RequiredEntityNotFoundException;
use ActiveCollab\DatabaseObject\Entity\EntityInterface;
use ActiveCollab\DatabaseObject\PoolInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class EntityFromRequestResolver implements EntityFromRequestResolverInterface
{
    /**
     * @var PoolInterface
     */
    private $pool;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(PoolInterface $pool, LoggerInterface $logger = null)
    {
        $this->pool = $pool;
        $this->logger = $logger;
    }

    public function getEntityFromRequest(
        ServerRequestInterface $request,
        string $entity_type,
        string $param_name,
        bool $get_param_from_body = false): ?EntityInterface
    {
        if ($get_param_from_body) {
            $entity_id = $this->getEntityIdFromParams($request->getParsedBody(), $param_name);
        } else {
            $entity_id = $this->getEntityIdFromParams($request->getQueryParams(), $param_name);
        }

        $result = $entity_id ? $this->pool->getById($entity_type, $entity_id) : null;

        if ($result instanceof EntityInterface) {
            return $result;
        }

        return null;
    }

    public function mustGetEntityFromRequest(
        ServerRequestInterface $request,
        string $entity_type,
        string $param_name,
        bool $get_param_from_body = false
    ): EntityInterface
    {
        if ($get_param_from_body) {
            $entity_id = $this->mustGetEntityIdFromParams($request, $entity_type, $request->getParsedBody(), $param_name);
        } else {
            $entity_id = $this->mustGetEntityIdFromParams($request, $entity_type, $request->getQueryParams(), $param_name);
        }

        $result = $entity_id ? $this->pool->getById($entity_type, $entity_id) : null;

        if (!$result instanceof EntityInterface) {
            throw new RequiredEntityNotFoundException(
                $entity_type,
                $request->getMethod(),
                $param_name,
                true,
                $entity_id
            );
        }

        return $result;
    }

    private function getEntityIdFromParams($params, string $param_name): ?int
    {
        return is_array($params) && array_key_exists($param_name, $params) && (is_int($params[$param_name]) || ctype_digit($params[$param_name])) ?
            (int) $params[$param_name] :
            null;
    }

    private function mustGetEntityIdFromParams(
        ServerRequestInterface $request,
        string $entity_type, $params,
        string $param_name
    ): int
    {
        if (is_array($params) && array_key_exists($param_name, $params)) {
            $entity_id = $params[$param_name];

            if ($entity_id && (is_int($entity_id) || ctype_digit($entity_id))) {
                return (int) $entity_id;
            }

            throw new RequiredEntityNotFoundException(
                $entity_type,
                $request->getMethod(),
                $param_name,
                true,
                $entity_id
            );
        } else {
            throw new RequiredEntityNotFoundException(
                $entity_type,
                $request->getMethod(),
                $param_name,
                false
            );
        }
    }
}
