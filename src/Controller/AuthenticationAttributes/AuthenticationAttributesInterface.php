<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Controller\AuthenticationAttributes;

use ActiveCollab\Authentication\Adapter\AdapterInterface;
use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticationResult\AuthenticationResultInterface;
use Psr\Http\Message\ServerRequestInterface;

interface AuthenticationAttributesInterface
{
    public function getAuthenticationAdapter(ServerRequestInterface $request): ? AdapterInterface;

    public function getAuthenticatedUser(ServerRequestInterface $request) : ? AuthenticatedUserInterface;

    public function getAuthenticatedWith(ServerRequestInterface $request) : ? AuthenticationResultInterface;
}
