<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Test\Encoder;

use ActiveCollab\Authentication\Adapter\AdapterInterface;
use ActiveCollab\Authentication\Adapter\BrowserSessionAdapter;
use ActiveCollab\Authentication\AuthenticationResult\Transport\Authentication\AuthenticationTransport;
use ActiveCollab\Bootstrap\Controller\ActionResultEncoder\ValueEncoder\AuthenticationTransportEncoder;
use ActiveCollab\Bootstrap\Test\Base\TestCase;
use ActiveCollab\Bootstrap\Test\Fixtures\ActionResultInContainer;
use ActiveCollab\Controller\ActionResult\StatusResult\StatusResult;
use ActiveCollab\Controller\ActionResultEncoder\ActionResultEncoder;
use ActiveCollab\Controller\ActionResultEncoder\ValueEncoder\ArrayEncoder;
use Pimple\Container;
use Psr\Http\Message\ResponseInterface;

class AuthenticationTransportEncoderTest extends TestCase
{
    /**
     * @var AdapterInterface
     */
    private $adapter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adapter = $this->createMock(BrowserSessionAdapter::class);
    }

    public function testShouldEncode()
    {
        $this->assertFalse((new AuthenticationTransportEncoder())->shouldEncode(null));
        $this->assertFalse((new AuthenticationTransportEncoder())->shouldEncode([1, 2, 3]));
        $this->assertFalse((new AuthenticationTransportEncoder())->shouldEncode(new StatusResult(200)));
        $this->assertTrue((new AuthenticationTransportEncoder())->shouldEncode(new AuthenticationTransport($this->adapter)));
    }

    public function testEncode()
    {
        $authentication_transport = new AuthenticationTransport($this->adapter, null, null, [1, 2, 3]);

        $encoder = new ActionResultEncoder(new ActionResultInContainer(new Container()));
        $encoder->addValueEncoder(new ArrayEncoder());

        $response = $this->createResponse()->withHeader('X-Test', 'yes');

        $response = (new AuthenticationTransportEncoder())->encode($response, $encoder, $authentication_transport);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertStringContainsString('yes', $response->getHeaderLine('X-Test'));
        $this->assertStringContainsString('application/json', $response->getHeaderLine('Content-Type'));

        $response_body = json_decode((string) $response->getBody(), true);

        $this->assertIsArray($response_body);
        $this->assertSame([1, 2, 3], $response_body);
    }
}
