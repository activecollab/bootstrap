<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Bootstrap\ResultEncoder;

use ActiveCollab\Controller\ResultEncoder\ResultEncoder as BaseResultEncoder;
use ActiveCollab\DatabaseConnection\Result\ResultInterface;
use ActiveCollab\DatabaseObject\CollectionInterface;
use ActiveCollab\DatabaseObject\ObjectInterface;
use ActiveCollab\Etag\EtagInterface;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\HttpCache\CacheProvider;

/**
 * @package ActiveCollab\Bootstrap\ResultEncoder
 */
class ResultEncoder extends BaseResultEncoder
{
    /**
     * @var CacheProvider
     */
    private $cache_provider;

    /**
     * @var string
     */
    private $app_identifier;

    /**
     * @var string
     */
    private $user_identifier;

    /**
     * @param CacheProvider $cache
     * @param string        $app_identifier
     * @param string        $user_identifier
     */
    public function __construct(CacheProvider $cache, $app_identifier, $user_identifier)
    {
        $this->cache_provider = $cache;
        $this->app_identifier = $app_identifier;
        $this->user_identifier = $user_identifier;
    }

    /**
     * {@inheritdoc}
     */
    protected function onNoEncoderApplied($action_result, ServerRequestInterface $request, ResponseInterface $response)
    {
        if ($action_result instanceof CollectionInterface) {
            return $this->encodeCollection($action_result, $response);
        } elseif ($action_result instanceof ObjectInterface) {
            return $this->encodeSingle($action_result, $response);
        } elseif ($action_result instanceof ResultInterface) {
            return $response->write(json_encode($action_result->toArray()))->withStatus(200);
        } else {
            return parent::onNoEncoderApplied($action_result, $request, $response);
        }
    }

    /**
     * @param  CollectionInterface $action_result
     * @param  ResponseInterface   $response
     * @return ResponseInterface
     */
    private function encodeCollection(CollectionInterface $action_result, ResponseInterface $response)
    {
        $response = $response->write(json_encode($action_result))->withStatus(200);

        if ($action_result->canBeEtagged()) {
            if ($action_result->getApplicationIdentifier() != $this->app_identifier) {
                $action_result->setApplicationIdentifier($this->app_identifier); // Sign collection etag with app identifier
            }

            $response = $this->cache_provider->withEtag($response, $action_result->getEtag($this->user_identifier));
            $response = $this->cache_provider->withExpires($response, '+90 days');
        }

        if ($action_result->getCurrentPage() && $action_result->getItemsPerPage()) {
            $response = $response->withHeader('X-PaginationCurrentPage', $action_result->getCurrentPage())
                ->withHeader('X-PaginationItemsPerPage', $action_result->getItemsPerPage())
                ->withHeader('X-PaginationTotalItems', $action_result->count());
        } else {
            $response = $response->withHeader('X-PaginationCurrentPage', 0)
                ->withHeader('X-PaginationItemsPerPage', 0)
                ->withHeader('X-PaginationTotalItems', $action_result->count());
        }

        return $response;
    }

    /**
     * @param  ObjectInterface   $action_result
     * @param  ResponseInterface $response
     * @return ResponseInterface
     */
    private function encodeSingle(ObjectInterface $action_result, ResponseInterface $response)
    {
        $result = ['single' => $action_result];

        foreach ($action_result->jsonSerializeDetails() as $k => $v) {
            if ($k == 'single') {
                throw new LogicException("JSON serialize details can't overwrite 'single' property");
            } else {
                $result[$k] = $v;
            }
        }

        $response = $response->write(json_encode($result))->withStatus(200);

        if ($action_result instanceof EtagInterface) {
            $response = $response->withHeader('Etag', $action_result->getEtag($this->user_identifier));
        }

        return $response;
    }
}
