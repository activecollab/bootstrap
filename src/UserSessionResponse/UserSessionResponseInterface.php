<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Bootstrap\UserSessionResponse;

use ActiveCollab\Authentication\AuthenticationResult\AuthenticationResultInterface;
use ActiveCollab\Controller\Response\ResponseInterface;
use ActiveCollab\User\UserInterface;

/**
 * @package ActiveCollab\Bootstrap\UserSessionResponse
 */
interface UserSessionResponseInterface extends ResponseInterface
{
    /**
     * @return AuthenticationResultInterface|null
     */
    public function getAuthenticatedWith();

    /**
     * @return UserInterface|null
     */
    public function getUser();

    /**
     * @return bool
     */
    public function getIsNew();

    /**
     * @return array
     */
    public function toArray();
}
