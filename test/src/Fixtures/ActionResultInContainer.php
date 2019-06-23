<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Test\Fixtures;

use ActiveCollab\Controller\ActionResult\Container\ActionResultContainerInterface;
use LogicException;
use Pimple\Container;
use RuntimeException;

class ActionResultInContainer implements ActionResultContainerInterface
{
    /**
     * @var Container
     */
    private $container;

    private $key;

    public function __construct(Container $container, $key = 'action_result')
    {
        $this->container = $container;
        $this->key = $key;
    }

    public function getValue()
    {
        if ($this->hasValue()) {
            return $this->container[$this->key];
        }

        throw new RuntimeException('Action result not found in the container.');
    }

    public function hasValue()
    {
        return $this->container->offsetExists($this->key);
    }

    public function &setValue($value)
    {
        $this->container[$this->key] = $value;

        return $this;
    }

    public function &removeValue()
    {
        throw new LogicException("Value can't be removed.");
    }
}
