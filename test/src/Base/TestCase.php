<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Test\Base;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\ResponseFactory;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Stream;

abstract class TestCase extends BaseTestCase
{
    protected $fixtures_dir;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->fixtures_dir = dirname(dirname(__DIR__)) . '/fixtures';
    }

    protected function createRequest(
        string $method = 'GET',
        string $path = '/',
        array $query_params = [],
        array $payload = null
    ): ServerRequestInterface
    {
        /** @var ServerRequestInterface $request */
        $request = (new ServerRequestFactory())
            ->createServerRequest($method, $this->prepareRequestUri($path, $query_params));

        $stream = new Stream('php://memory', 'r+');
        $stream->write(json_encode($payload));

        $request
            ->withHeader('Content-Type', 'application/json')
            ->withBody($stream);

        return $request;
    }

    private function prepareRequestUri(string $path, array $query_params): string
    {
        $result = '/' . trim($path, '/');

        if (!empty($query_params)) {
            $result .= '?' . http_build_query($query_params);
        }

        return $result;
    }

    protected function createResponse(): ResponseInterface
    {
        return (new ResponseFactory())->createResponse();
    }
}
