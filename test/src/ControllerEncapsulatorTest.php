<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Test;

use ActiveCollab\Bootstrap\Controller\Encapsulator\Encapsulator;
use ActiveCollab\Bootstrap\Test\Base\TestCase;
use ActiveCollab\Bootstrap\Test\Fixtures\TestController;
use Psr\Http\Message\ResponseInterface;

class ControllerEncapsulatorTest extends TestCase
{
    public function testEncapsulation()
    {
        $encapsulator = new Encapsulator(new TestController());
        $this->assertInstanceOf(TestController::class, $encapsulator->getController());

        /** @var ResponseInterface $response */
        $response = call_user_func($encapsulator, $this->createRequest(), $this->createResponse()->withHeader('X-Test', 'yes!'), []);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame('yes!', $response->getHeaderLine('X-Test'));
    }
}
