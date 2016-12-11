<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Controller\ActionResultEncoder\ValueEncoder;

use ActiveCollab\Controller\ActionResultEncoder\ActionResultEncoderInterface;
use ActiveCollab\Controller\ActionResultEncoder\ValueEncoder\ValueEncoder;
use ActiveCollab\DatabaseObject\CollectionInterface;
use Psr\Http\Message\ResponseInterface;

class DatabaseCollectionEncoder extends ValueEncoder
{
    public function shouldEncode($value): bool
    {
        return $value instanceof CollectionInterface;
    }

    /**
     * @param  ResponseInterface            $response
     * @param  ActionResultEncoderInterface $encoder
     * @param  CollectionInterface          $value
     * @return ResponseInterface
     */
    public function encode(ResponseInterface $response, ActionResultEncoderInterface $encoder, $value): ResponseInterface
    {
        /** @var ResponseInterface $response */
        $response = $response
            ->withBody($this->createBodyFromText(json_encode($value)))
            ->withStatus(200);

        if ($value->isPaginated()) {
            $response = $response->withHeader('X-PaginationCurrentPage', $value->getCurrentPage())
                ->withHeader('X-PaginationItemsPerPage', $value->getItemsPerPage())
                ->withHeader('X-PaginationTotalItems', $value->count());
        }

        return $response;
    }
}
