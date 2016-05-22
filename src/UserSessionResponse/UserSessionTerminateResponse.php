<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Bootstrap\UserSessionResponse;

/**
 * @package ActiveCollab\Bootstrap\UserSessionResponse
 */
class UserSessionTerminateResponse implements UserSessionTerminateResponseInterface
{
    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return ['is_ok' => true];
    }
}
