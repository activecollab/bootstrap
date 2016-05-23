<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare (strict_types = 1);

namespace ActiveCollab\Bootstrap\TestCase;

use ActiveCollab\ContainerAccess\ContainerAccessInterface;
use ActiveCollab\ContainerAccess\ContainerAccessInterface\Implementation as ContainerAccessInterfaceImplementation;
use ActiveCollab\DateValue\DateTimeValue;
use ActiveCollab\DateValue\DateTimeValueInterface;
use ActiveCollab\User\UnidentifiedVisitor;
use Doctrine\Common\Inflector\Inflector;
use Interop\Container\ContainerInterface;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Slim\Container;

/**
 * @property string $app_env
 * @property string $app_root
 * @property string $app_name
 * @property string $app_version
 * @property string $app_identifier
 * @property string $user_identifier
 * @property \Monolog\Handler\TestHandler|\Monolog\Handler\HandlerInterface $logger_handler
 * @property \Psr\Log\LoggerInterface $logger
 *
 * @package ActiveCollab\Bootstrap\TestCase
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase implements ContainerAccessInterface
{
    use ContainerAccessInterfaceImplementation;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var DateTimeValue|null
     */
    protected $now;

    /**
     * Set up test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->container = new Container();
        $this->setNow(new DateTimeValue());

        $this->addToContainer('app_env', function () {
            return 'testing';
        });

        $this->addToContainer('app_root', function () {
            return dirname(__DIR__, 5);
        });

        $this->addToContainer('app_name', function ($c) {
            return basename($c['app_root']);
        });

        $this->addToContainer('app_version', function () {
            return '1.0.0';
        });

        $this->addToContainer('app_identifier', function ($c) {
            return "{$c['app_name']} v{$c['app_version']}";
        });

        $this->addToContainer('user_identifier', function () {
            return (new UnidentifiedVisitor())->getEmail();
        });

        $this->addToContainer('logger_handler', function () {
            return new TestHandler();
        });

        $this->addToContainer('logger', function ($c) {
            return new Logger("{$c['app_name']} Test", [$c['logger_handler']]);
        });
    }

    /**
     * Tear down test environment.
     */
    public function tearDown()
    {
        $this->container = null;
        $this->setNow(null);

        parent::tearDown();
    }

    /**
     * Add callback value resolver to container.
     *
     * @param string   $key
     * @param callable $callback
     */
    protected function addToContainer($key, callable $callback)
    {
        $this->getContainer()[$key] = $callback;
    }

    /**
     * Set a value in container.
     *
     * @param string $key
     * @param mixed  $value
     */
    protected function setInContainer($key, $value)
    {
        $this->getContainer()[$key] = $value;
    }

    /**
     * @return DateTimeValue|null
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

    /**
     * Return environment variable prefix.
     *
     * @return string
     */
    protected function getEnvVariablePrefix(): string
    {
        return strtoupper(Inflector::tableize($this->app_name)) . '_';
    }
}
