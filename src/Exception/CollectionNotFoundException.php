<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Bootstrap\Exception;

use Exception;
use RuntimeException;

/**
 * @package ActiveCollab\Bootstrap\Exception
 */
class CollectionNotFoundException extends RuntimeException
{
    /**
     * @param string         $controller
     * @param string         $action
     * @param string         $collection
     * @param Exception|null $previous
     */
    public function __construct($controller, $action, $collection, Exception $previous = null)
    {
        parent::__construct("Collection '$collection' not found. It is required for '$action' of '$controller' controller", 0, $previous);
    }
}
