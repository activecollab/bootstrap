<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Bootstrap\Controller;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticationResult\AuthenticationResultInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package ActiveCollab\Bootstrap\Controller
 */
trait ControllerTrait
{
    /**
     * Return authenticated user.
     *
     * @param  ServerRequestInterface          $request
     * @return AuthenticatedUserInterface|null
     */
    protected function getAuthenticatedUser(ServerRequestInterface $request)
    {
        return $request->getAttribute($this->getAuthenticatedUserAttributeName());
    }

    /**
     * Return authenticated user request attribute name.
     *
     * @return string
     */
    protected function getAuthenticatedUserAttributeName(): string
    {
        return 'authenticated_user';
    }

    /**
     * Return authentication method instance (token or session).
     *
     * @param  ServerRequestInterface             $request
     * @return AuthenticationResultInterface|null
     */
    protected function getAuthenticatedWith(ServerRequestInterface $request)
    {
        return $request->getAttribute($this->getAuthenticatedWithAttributeName());
    }

    /**
     * Return name of the request attribute where authentication method is stored.
     *
     * @return string
     */
    protected function getAuthenticatedWithAttributeName(): string
    {
        return 'authenticated_with';
    }
}
