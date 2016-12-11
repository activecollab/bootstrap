<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Controller\ActionResultEncoder\ValueEncoder;

use ActiveCollab\Authentication\AuthenticationResult\Transport\TransportInterface;
use ActiveCollab\Controller\ActionResultEncoder\ActionResultEncoderInterface;
use ActiveCollab\Controller\ActionResultEncoder\ValueEncoder\ValueEncoder;
use Psr\Http\Message\ResponseInterface;

class AuthenticationTransportEncoder extends ValueEncoder
{
    public function shouldEncode($value): bool
    {
        return $value instanceof TransportInterface;
    }

    /**
     * @param  ResponseInterface            $response
     * @param  ActionResultEncoderInterface $encoder
     * @param  TransportInterface           $value
     * @return ResponseInterface
     */
    public function encode(ResponseInterface $response, ActionResultEncoderInterface $encoder, $value): ResponseInterface
    {
        return $encoder->encode($response, $value->getPayload());
    }
}
