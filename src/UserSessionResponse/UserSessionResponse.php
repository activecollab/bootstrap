<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Bootstrap\UserSessionResponse;

use ActiveCollab\Authentication\AuthenticationResult\AuthenticationResultInterface;
use ActiveCollab\User\UserInterface;

/**
 * @package ActiveCollab\Bootstrap\UserSessionResponse
 */
class UserSessionResponse implements UserSessionResponseInterface
{
    /**
     * @var AuthenticationResultInterface|null
     */
    private $authenticated_with;

    /**
     * @var UserInterface|null
     */
    private $user;

    /**
     * @var bool
     */
    private $is_new;

    /**
     * @param AuthenticationResultInterface|null $authenticated_with
     * @param UserInterface|null                 $user
     * @param bool                               $is_new
     */
    public function __construct(AuthenticationResultInterface $authenticated_with = null, UserInterface $user = null, $is_new = false)
    {
        $this->authenticated_with = $authenticated_with;
        $this->user = $user;
        $this->is_new = $is_new;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthenticatedWith()
    {
        return $this->authenticated_with;
    }

    /**
     * {@inheritdoc}
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsNew()
    {
        return $this->is_new;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return ['authenticated_user' => $this->user, 'authenticated_with' => $this->authenticated_with];
    }
}
