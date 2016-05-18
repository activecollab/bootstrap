<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Bootstrap\TestCase;

use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Slim\CallableResolver;
use Slim\Handlers\Strategies\RequestResponse;
use Slim\Http\Body;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Uri;

/**
 * @package ActiveCollab\Bootstrap\TestCase
 */
abstract class RequestResponseTest extends ModelTestCase
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * Run before each test.
     */
    public function setUp()
    {
        parent::setUp();

        $uri = Uri::createFromString('https://example.com:443/foo/bar?abc=123');
        $this->request = new Request('GET', $uri, new Headers(), [], Environment::mock()->all(), new Body(fopen('php://temp', 'r+')));
        $this->response = new Response();

        // Prepare container and populate it with Slim services that are needed for further tests
        $this->addToContainer('callableResolver', function ($c) {
            return new CallableResolver($c);
        });

        $this->addToContainer('foundHandler', function ($c) {
            return new RequestResponse();
        });
    }

    /**
     * Parse response body as JSON.
     *
     * @param  ResponseInterface $response
     * @return mixed
     */
    protected function getJsonFromResponse(ResponseInterface $response)
    {
        $this->assertEquals('application/json;charset=UTF-8', $response->getHeaderLine('Content-Type'));

        $json = json_decode((string) $response->getBody(), true);

        if (json_last_error()) {
            throw new RuntimeException('JSON parse failed: ' . json_last_error_msg());
        }

        return $json;
    }
}
