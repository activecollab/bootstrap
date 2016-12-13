<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Test;

use ActiveCollab\Authentication\Adapter\AdapterInterface;
use ActiveCollab\Authentication\Adapter\BrowserSessionAdapter;
use ActiveCollab\Authentication\AuthenticatedUser\AuthenticatedUserInterface;
use ActiveCollab\Authentication\AuthenticatedUser\RepositoryInterface;
use ActiveCollab\Authentication\Session\SessionInterface;
use ActiveCollab\Bootstrap\Test\Base\TestCase;
use ActiveCollab\Bootstrap\Test\Fixtures\TestController;
use ActiveCollab\Bootstrap\Test\Fixtures\AuthenticatedUser;


class AuthenticationAttributesTest extends TestCase
{
    public function testAuthenticationAdapter()
    {
        $request = $this->createRequest()->withAttribute('authentication_adapter', $this->createMock(BrowserSessionAdapter::class));

        $this->assertInstanceOf(AdapterInterface::class, (new TestController())->getAuthenticationAdapter($request));
    }

    public function testAuthenticatedUser()
    {
        $request = $this->createRequest()->withAttribute('authenticated_user', new AuthenticatedUser(1, 'john.doe@example.com', 'Jonh Doe', '123'));

        $this->assertInstanceOf(AuthenticatedUserInterface::class, (new TestController())->getAuthenticatedUser($request));
    }

    public function testAuthenticatedWith()
    {
        $request = $this->createRequest()->withAttribute('authenticated_with', new class() implements SessionInterface
        {
            public function getSessionId()
            {
                return '123';
            }
            public function getSessionTtl()
            {
                return 3600;
            }

            public function extendSession($reference_timestamp = null)
            {
            }

            public function getAuthenticatedUser(RepositoryInterface $repository)
            {
            }

            public function jsonSerialize()
            {
                return [];
            }
        });

        $this->assertInstanceOf(SessionInterface::class, (new TestController())->getAuthenticatedWith($request));
    }
}
