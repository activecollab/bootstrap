<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Bootstrap\ResultEncoder;

use ActiveCollab\Authentication\Session\SessionInterface;
use ActiveCollab\Bootstrap\UserSessionResponse\UserSessionResponseInterface;
use ActiveCollab\Bootstrap\UserSessionResponse\UserSessionTerminateResponseInterface;
use ActiveCollab\Controller\ResultEncoder\ResultEncoder as BaseResultEncoder;
use ActiveCollab\Cookies\CookiesInterface;
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
     * @var CookiesInterface|null
     */
    private $cookies_provider;

    /**
     * @var string
     */
    private $user_session_id_cookie_name;

    /**
     * @param CacheProvider    $cache_provider
     * @param string           $app_identifier
     * @param string           $user_identifier
     * @param CookiesInterface $cookies_provider
     * @param string           $user_session_id_cookie_name
     */
    public function __construct(CacheProvider $cache_provider, $app_identifier, $user_identifier, CookiesInterface $cookies_provider = null, $user_session_id_cookie_name = null)
    {
        $this->cache_provider = $cache_provider;
        $this->app_identifier = $app_identifier;
        $this->user_identifier = $user_identifier;
        $this->cookies_provider = $cookies_provider;
        $this->user_session_id_cookie_name = $user_session_id_cookie_name;
    }

    /**
     * {@inheritdoc}
     */
    protected function onNoEncoderApplied($action_result, ServerRequestInterface $request, ResponseInterface $response)
    {
        if ($action_result instanceof UserSessionResponseInterface) {
            return $this->encodeUserSessionResponse($action_result, $request, $response);
        } elseif ($action_result instanceof UserSessionTerminateResponseInterface) {
            return $this->encodeUserSessionTerminatedResponse($action_result, $request, $response);
        } elseif ($action_result instanceof CollectionInterface) {
            return $this->encodeCollection($action_result, $response);
        } elseif ($action_result instanceof ObjectInterface) {
            return $this->encodeSingle($action_result, $response);
        } elseif ($action_result instanceof ResultInterface) {
            return $this->encodeArray($action_result->toArray(), $response);
        } else {
            return parent::onNoEncoderApplied($action_result, $request, $response);
        }
    }

    /**
     * Encode DataObject collection.
     *
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
     * Encode individual DataObject object.
     *
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

    /**
     * Encode user session action results and return properly populated response.
     *
     * @param  UserSessionResponseInterface $action_result
     * @param  ServerRequestInterface       $request
     * @param  ResponseInterface            $response
     * @return ResponseInterface
     */
    private function encodeUserSessionResponse(UserSessionResponseInterface $action_result, ServerRequestInterface $request, ResponseInterface $response)
    {
        if ($action_result->getAuthenticatedWith() instanceof  SessionInterface) {
            $response = $this->getCookiesProvider()->set($request, $response, $this->getUserSessionIdCookieName(), $action_result->getAuthenticatedWith()->getSessionId(), [
                'ttl' => 1209600,
                'http_only' => true,
            ])[1];
        }

        return $this->encodeArray($action_result->toArray(), $response);
    }

    /**
     * Encode user session terminated action results and return properly populated response.
     *
     * @param  UserSessionTerminateResponseInterface $action_result
     * @param  ServerRequestInterface                $request
     * @param  ResponseInterface                     $response
     * @return ResponseInterface
     */
    private function encodeUserSessionTerminatedResponse(UserSessionTerminateResponseInterface $action_result, ServerRequestInterface $request, ResponseInterface $response)
    {
        $response = $this->getCookiesProvider()->remove($request, $response, $this->getUserSessionIdCookieName())[1];

        return $this->encodeArray($action_result->toArray(), $response);
    }

    /**
     * @return CacheProvider
     */
    protected function getCacheProvider()
    {
        return $this->cache_provider;
    }

    /**
     * @return string
     */
    protected function getAppIdentifier()
    {
        return $this->app_identifier;
    }

    /**
     * @return string
     */
    protected function getUserIdentifier()
    {
        return $this->user_identifier;
    }

    /**
     * @return CookiesInterface|null
     */
    protected function getCookiesProvider()
    {
        return $this->cookies_provider;
    }

    /**
     * @return string|null
     */
    protected function getUserSessionIdCookieName()
    {
        return $this->user_session_id_cookie_name;
    }
}
