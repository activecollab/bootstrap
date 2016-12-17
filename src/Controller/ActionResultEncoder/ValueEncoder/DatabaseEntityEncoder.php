<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Controller\ActionResultEncoder\ValueEncoder;

use ActiveCollab\Controller\ActionResultEncoder\ActionResultEncoderInterface;
use ActiveCollab\Controller\ActionResultEncoder\ValueEncoder\JsonContentTypeTrait;
use ActiveCollab\Controller\ActionResultEncoder\ValueEncoder\ValueEncoder;
use ActiveCollab\DatabaseObject\Entity\EntityInterface;
use LogicException;
use Psr\Http\Message\ResponseInterface;

class DatabaseEntityEncoder extends ValueEncoder
{
    use JsonContentTypeTrait;

    public function shouldEncode($value): bool
    {
        return $value instanceof EntityInterface;
    }

    /**
     * @param  ResponseInterface            $response
     * @param  ActionResultEncoderInterface $encoder
     * @param  EntityInterface              $value
     * @return ResponseInterface
     */
    public function encode(ResponseInterface $response, ActionResultEncoderInterface $encoder, $value): ResponseInterface
    {
        if (array_key_exists('single', $value->jsonSerializeDetails())) {
            throw new LogicException("JSON serialize details can't overwrite 'single' property.");
        }

        $response = $this->setJsonContentType($response);

        $result = array_merge(['single' => $value], $value->jsonSerializeDetails());

        return $response->withBody($this->createBodyFromText(json_encode($result)));
    }
}
