<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Bootstrap\Controller;

use ActiveCollab\User\UserInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package ActiveCollab\Bootstrap\Controller
 */
trait ControllerTrait
{
    /**
     * Return authenticated user.
     *
     * @param  ServerRequestInterface $request
     * @return UserInterface|null
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
}
