<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Test\Fixtures;

use ActiveCollab\Bootstrap\Controller\Controller;
use ActiveCollab\Controller\ActionNameResolver\ActionNameResolverInterface;
use ActiveCollab\Controller\ActionResult\Container\ActionResultContainerInterface;
use Pimple\Container;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class TestController extends Controller
{
    public function __construct(ActionNameResolverInterface $action_name_resolver = null, ActionResultContainerInterface $action_result_container = null, LoggerInterface $logger = null)
    {
        if (empty($action_name_resolver)) {
            $action_name_resolver = new class() implements ActionNameResolverInterface {
                public function getActionName(ServerRequestInterface $request): string
                {
                    return 'index';
                }
            };
        }

        if (empty($action_result_container)) {
            $action_result_container = new ActionResultInContainer(new Container());
        }

        parent::__construct($action_name_resolver, $action_result_container, $logger);
    }

    public function index()
    {
        return [1, 2, 3];
    }
}
