<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\AppBootstrapper;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package ActiveCollab\Shepherd\Utils
 */
interface AppBootstrapperInterface
{
    /**
     * Return true if app is bootstrapped.
     *
     * @return bool
     */
    public function isBootstrapped(): bool;

    /**
     * @return AppBootstrapperInterface
     */
    public function &bootstrap(): AppBootstrapperInterface;

    /**
     * Return true if app was ran.
     *
     * @return bool
     */
    public function isRan(): bool;

    /**
     * Run the bootstrapped app and emit the response.
     *
     * Set $silent to FALSE if you do not want result to be emitted to the output buffer.
     *
     * @param  bool                     $silent
     * @return AppBootstrapperInterface
     */
    public function &run(bool $silent = false): AppBootstrapperInterface;

    /**
     * Log response.
     *
     * @return AppBootstrapperInterface
     */
    public function &logResponse(): AppBootstrapperInterface;

    /**
     * Process the request and get the response. This method is userful for testing the full middleware stack execution.
     *
     * @param  ServerRequestInterface $request
     * @param  ResponseInterface      $response
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface;
}
