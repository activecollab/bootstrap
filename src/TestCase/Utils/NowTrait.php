<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\TestCase\Utils;

use ActiveCollab\DateValue\DateTimeValue;
use ActiveCollab\DateValue\DateTimeValueInterface;

/**
 * @package ActiveCollab\Bootstrap\TestCase\Utils
 */
trait NowTrait
{
    /**
     * @var DateTimeValueInterface|null
     */
    protected $now;

    /**
     * @return DateTimeValueInterface
     */
    public function getNow()
    {
        return $this->now;
    }

    /**
     * @param DateTimeValueInterface|null $now
     */
    public function setNow(DateTimeValueInterface $now = null)
    {
        $this->now = $now;
        DateTimeValue::setTestNow($this->now);
    }
}
