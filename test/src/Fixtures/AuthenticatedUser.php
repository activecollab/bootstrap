<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Test\Fixtures;

use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\User\UserInterface\ImplementationUsingFullName;

class AuthenticatedUser implements AuthenticatedUserInterface
{
    use ImplementationUsingFullName;

    private $id;

    private $name;

    private $email;

    private $password;

    private $can_authenticate;

    public function __construct($id, $email, $name, $password, $can_authenticate = true)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->can_authenticate = $can_authenticate;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getFullName()
    {
        return $this->name;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getUsername()
    {
        return $this->getEmail();
    }

    public function isValidPassword($password)
    {
        return $password === $this->password;
    }

    public function canAuthenticate()
    {
        return $this->can_authenticate;
    }

    public function jsonSerialize()
    {
        return ['id' => $this->getId(), 'name' => $this->getFullName(), 'email' => $this->getEmail()];
    }
}
