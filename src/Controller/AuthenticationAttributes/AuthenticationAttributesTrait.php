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

trait AuthenticationAttributesTrait
{
    public function getAuthenticationAdapter(ServerRequestInterface $request): ? AdapterInterface
    {
        return $request->getAttribute($this->getAuthenticateAdapterAttributeName());
    }

    protected function getAuthenticateAdapterAttributeName(): string
    {
        return 'authentication_adapter';
    }

    public function getAuthenticatedUser(ServerRequestInterface $request): ? AuthenticatedUserInterface
    {
        return $request->getAttribute($this->getAuthenticatedUserAttributeName());
    }

    protected function getAuthenticatedUserAttributeName(): string
    {
        return 'authenticated_user';
    }

    public function getAuthenticatedWith(ServerRequestInterface $request): ? AuthenticationResultInterface
    {
        return $request->getAttribute($this->getAuthenticatedWithAttributeName());
    }

    protected function getAuthenticatedWithAttributeName(): string
    {
        return 'authenticated_with';
    }
}
