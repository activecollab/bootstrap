<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Bootstrap\TestCase;

use ActiveCollab\DateValue\DateTimeValue;

/**
 * @package ActiveCollab\Bootstrap\TestCase
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DateTimeValue
     */
    protected $now;

    /**
     * Set up test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->now = new DateTimeValue();

        DateTimeValue::setTestNow($this->now);
    }

    /**
     * Tear down test environment.
     */
    public function tearDown()
    {
        DateTimeValue::setTestNow(null);

        parent::tearDown();
    }

    /**
     * @return string
     */
    protected function getAppRoot()
    {
        return dirname(dirname(dirname(__DIR__)));
    }

    /**
     * @return string
     */
    protected function getAppName()
    {
        return basename($this->getAppRoot());
    }

    /**
     * @return string
     */
    protected function getAppVersion()
    {
        return '1.0.0';
    }

    /**
     * @return string
     */
    protected function getAppIdentifier()
    {
        return "{$this->getAppName()} v{$this->getAppVersion()}";
    }

    /**
     * Return environment variable prefix.
     *
     * @return string
     */
    protected function getEnvVariablePrefix()
    {
        return strtoupper($this->getAppName()) . '_';
    }
}
