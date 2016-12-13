<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Bootstrap\Controller\Encapsulator;

use ActiveCollab\Controller\ControllerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface EncapsulatorInterface
{
    public function getController(): ControllerInterface;

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $args): ResponseInterface;
}
