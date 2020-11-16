<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Test\Base;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class TestCase extends BaseTestCase
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
    protected function createRequest(
        string $method = 'GET',
        string $path = '/',
        array $query_params = [],
        array $payload = []
    ): ServerRequestInterface
    {
        return (new ServerRequestFactory())->createServerRequest(
            $method,
            '/' . trim($path, '/')
        )
            ->withQueryParams($query_params)
            ->withParsedBody($payload);
    }

    protected function createResponse(): ResponseInterface
    {
        return (new ResponseFactory())->createResponse();
    }
}
