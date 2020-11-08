<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\TestCase\FullStack;

use ActiveCollab\Bootstrap\AppBootstrapper\AppBootstrapperInterface;
use ActiveCollab\Bootstrap\TestCase\Utils\NowTrait;
use ActiveCollab\DateValue\DateTimeValue;
use PHPUnit\Framework\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    use NowTrait;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setNow(new DateTimeValue());
    }

    protected function tearDown(): void
    {
        $this->setNow(null);

        parent::tearDown();
    }

    /**
     * @return AppBootstrapperInterface
     */
    abstract protected function getAppBootstrapper(): AppBootstrapperInterface;

    /**
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        $app_boostrapper = $this->getAppBootstrapper();

        if (!$app_boostrapper->isBootstrapped()) {
            $app_boostrapper->bootstrap();
        }

        if ($app_boostrapper->getContainer()->has($name)) {
            return $app_boostrapper->getContainer()->get($name);
        }

        throw new RuntimeException(sprintf('Property %s not found in class %s', $name, get_class($this)));
    }
}
