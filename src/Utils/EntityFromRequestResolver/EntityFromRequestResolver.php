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
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Interfaces\RouteInterface;

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
        string $param_source = self::PARAM_SOURCE_ROUTE_ARGUMENT
    ): ?EntityInterface
    {
        $entity_id = $this->getEntityIdFromParams(
            $this->getParamsFromSource($request, $param_source),
            $param_name
        );

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
        string $param_source = self::PARAM_SOURCE_ROUTE_ARGUMENT
    ): EntityInterface
    {
        $entity_id = $this->mustGetEntityIdFromParams(
            $request,
            $entity_type,
            $this->getParamsFromSource($request, $param_source),
            $param_name
        );

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

    private function getParamsFromSource(ServerRequestInterface $request, string $param_source): array
    {
        $params = null;

        switch ($param_source) {
            case self::PARAM_SOURCE_ROUTE_ARGUMENT:
                $route = $request->getAttribute('route');

                return $route instanceof RouteInterface ? $route->getArguments() : [];
            case self::PARAM_SOURCE_QUERY_STRING:
                return $request->getQueryParams();
            case self::PARAM_SOURCE_BODY_PARAM:
                $params = $request->getParsedBody();

                if (!is_array($params)) {
                    $params = [];
                }

                return $params;
            default:
                throw new InvalidArgumentException("Unknown param source '$param_source'.");
        }
    }

    private function getEntityIdFromParams(array $params, string $param_name): ?int
    {
        return array_key_exists($param_name, $params) && (is_int($params[$param_name]) || ctype_digit($params[$param_name])) ?
            (int) $params[$param_name] :
            null;
    }

    private function mustGetEntityIdFromParams(
        ServerRequestInterface $request,
        string $entity_type,
        array $params,
        string $param_name
    ): int
    {
        if (array_key_exists($param_name, $params)) {
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
