<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Bootstrap\Test\Base;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Environment as SlimEnvironment;
use Slim\Http\Request as SlimRequest;
use Slim\Http\Response as SlimResponse;

/**
 * @package ActiveCollab\Bootstrap\Test\Base
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Prepare server request based on the given arguments.
     *
     * @param  string                 $method
     * @param  string                 $path
     * @param  array                  $query_params
     * @param  array                  $payload
     * @return ServerRequestInterface
     */
    protected function createRequest(string $method = 'GET', string $path = '/', array $query_params = [], array $payload = []): ServerRequestInterface
    {
        $environment_user_data = [
            'REQUEST_METHOD' => $method,
            'REQUEST_URI' => '/' . trim($path, '/'),
        ];

        if (!empty($query_params)) {
            $environment_user_data['QUERY_STRING'] = http_build_query($query_params);
        }

        $environment = SlimEnvironment::mock($environment_user_data);

        $request = SlimRequest::createFromEnvironment($environment)
            ->withParsedBody($payload);

        return $request;
    }

    /**
     * @return ResponseInterface
     */
    protected function createResponse(): ResponseInterface
    {
        return new SlimResponse();
    }
}
