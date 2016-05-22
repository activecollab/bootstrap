<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Bootstrap\UserSessionResponse;

use ActiveCollab\Controller\Response\ResponseInterface;

/**
 * @package ActiveCollab\Bootstrap\UserSessionResponse
 */
interface UserSessionTerminateResponseInterface extends ResponseInterface
{
    /**
     * @return array
     */
    public function toArray();
}
