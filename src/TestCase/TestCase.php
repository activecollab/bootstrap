<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\TestCase;

use ActiveCollab\Bootstrap\TestCase\Utils\NowTrait;
use ActiveCollab\DateValue\DateTimeValue;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use NowTrait;

    public function setUp()
    {
        parent::setUp();

        $this->setNow(new DateTimeValue());
    }

    public function tearDown()
    {
        $this->setNow(null);

        parent::tearDown();
    }
}
