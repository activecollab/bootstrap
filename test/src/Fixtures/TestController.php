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
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class TestController extends Controller
{
    public function __construct(ActionNameResolverInterface $action_name_resolver = null, string $action_result_attribute_name = 'action_result', LoggerInterface $logger = null)
    {
        if (empty($action_name_resolver)) {
            $action_name_resolver = new class() implements ActionNameResolverInterface {
                public function getActionName(ServerRequestInterface $request): string
                {
                    return 'index';
                }
            };
        }

        parent::__construct($action_name_resolver, $action_result_attribute_name, $logger);
    }

    public function index()
    {
        return [1, 2, 3];
    }
}
