<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Controller\ActionNameResolver;

use ActiveCollab\Controller\ActionNameResolver\ActionNameResolverInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Slim\Interfaces\RouteInterface;

/**
 * @package ActiveCollab\Bootstrap\Controller\ActionNameResolver
 */
class ActionNameFromSlimRoute implements ActionNameResolverInterface
{
    /**
     * @var string
     */
    private $route_request_attribute_name;

    /**
     * @var string
     */
    private $action_route_argument_name;

    /**
     * @param string $route_request_attribute_name
     * @param string $action_route_argument_name
     */
    public function __construct(string $route_request_attribute_name = 'route', string $action_route_argument_name = '%s_action')
    {
        $this->route_request_attribute_name = $route_request_attribute_name;
        $this->action_route_argument_name = $action_route_argument_name;
    }

    /**
     * {@inheritdoc}
     */
    public function getActionName(ServerRequestInterface $request): string
    {
        $route = $request->getAttribute($this->route_request_attribute_name);

        if ($route instanceof RouteInterface) {
            $action_name = $route->getArgument($this->getActionArgumentName($request));

            if ($action_name) {
                return $action_name;
            } else {
                throw new RuntimeException("Action name not found for {$request->getMethod()} method.");
            }
        } else {
            throw new RuntimeException("Request attribute '{$this->route_request_attribute_name}' not found in the request.");
        }
    }

    /**
     * @param  ServerRequestInterface $request
     * @return string
     */
    protected function getActionArgumentName(ServerRequestInterface $request): string
    {
        return sprintf($this->action_route_argument_name, $request->getMethod());
    }
}
