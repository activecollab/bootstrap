<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\AppBootstrapper\Web;

use ActiveCollab\Bootstrap\AppBootstrapper\AppBootstrapperInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface WebAppBootstrapperInterface extends AppBootstrapperInterface
{
    /**
     * Return the response.
     *
     * If app was not ran, this function will throw an exception.
     *
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface;

    /**
     * Process the request and get the response. This method is userful for testing the full middleware stack execution.
     *
     * @param  ServerRequestInterface $request
     * @param  ResponseInterface      $response
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface;
}
