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
use ActiveCollab\DatabaseConnection\Result\ResultInterface;
use Psr\Http\Message\ResponseInterface;

class DatabaseResultEncoder extends ValueEncoder
{
    use JsonContentTypeTrait;

    public function shouldEncode($value): bool
    {
        return $value instanceof ResultInterface;
    }

    /**
     * @param  ResponseInterface            $response
     * @param  ActionResultEncoderInterface $encoder
     * @param  ResultInterface              $value
     * @return ResponseInterface
     */
    public function encode(ResponseInterface $response, ActionResultEncoderInterface $encoder, $value): ResponseInterface
    {
        $response = $this->setJsonContentType($response);

        $result = '[';

        foreach ($value as $row) {
            $result .= json_encode($row) . ',';
        }

        $result = rtrim($result, ',') . ']';

        return $response->withBody($this->createBodyFromText($result));
    }
}
