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
class RequiredEntityNotFoundException extends RuntimeException
{
    /**
     * @param string         $entity_type
     * @param string         $request_method
     * @param string         $request_param_name
     * @param bool           $request_param_found
     * @param mixed|null     $request_param_value
     * @param Exception|null $previous
     */
    public function __construct(
        string $entity_type,
        string $request_method,
        string $request_param_name,
        bool $request_param_found = false,
        $request_param_value = null,
        Exception $previous = null)
    {
        $message = "Failed to load {$entity_type} from {$request_method} request params. ";

        if ($request_param_found) {
            if (ctype_digit($request_param_value)) {
                $message .= "Param {$request_param_name} found, but entity #{$request_param_value} not found.";
            } elseif ($request_param_value) {
                $message .= "Param {$request_param_name} found, but it is not a numeric value.";
            } else {
                $message .= "Param {$request_param_name} found, but it is empty.";
            }
        } else {
            $message .= "Param {$request_param_name} not found.";
        }

        parent::__construct($message, 0, $previous);
    }
}
