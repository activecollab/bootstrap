<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare (strict_types = 1);

namespace ActiveCollab\Bootstrap\TestCase;

use ActiveCollab\DatabaseConnection\Connection\MysqliConnection;
use ActiveCollab\DatabaseConnection\ConnectionInterface;
use Doctrine\Common\Inflector\Inflector;
use mysqli;
use RuntimeException;

/**
 * @property mysqli $link
 * @property ConnectionInterface $connection
 *
 * @package ActiveCollab\Id\Test
 */
abstract class DatabaseTestCase extends TestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->addToContainer('link', function ($c) {
            $db_host = $this->getTestMySqlConnectionParam('host', 'localhost');
            $db_port = $this->getTestMySqlConnectionParam('port', 3306);
            $db_user = $this->getTestMySqlConnectionParam('user', 'root');
            $db_pass = $this->getTestMySqlConnectionParam('pass', '');
            $db_name = $this->getTestMySqlConnectionParam('database', $this->getTestMySqlDatabaseName($c['app_name']));

            $link = new \MySQLi("$db_host:$db_port", $db_user, $db_pass);

            if ($link->connect_error) {
                throw new RuntimeException('Failed to connect to database. MySQL said: ' . $link->connect_error);
            }

            if (!$link->select_db($db_name)) {
                throw new RuntimeException('Failed to select database');
            }

            return $link;
        });

        $this->addToContainer('connection', function ($c) {
            $connection = new MysqliConnection($c['link']);

            $connection->execute('SET foreign_key_checks = 0;');
            foreach ($connection->getTableNames() as $table_name) {
                $connection->dropTable($table_name);
            }
            $connection->execute('SET foreign_key_checks = 1;');

            return $connection;
        });
    }

    /**
     * Tear down test environment.
     */
    public function tearDown()
    {
        $this->connection->execute('SET foreign_key_checks = 0;');
        foreach ($this->connection->getTableNames() as $table_name) {
            $this->connection->dropTable($table_name);
        }
        $this->connection->execute('SET foreign_key_checks = 1;');

        $this->link->close();

        parent::tearDown();
    }

    /**
     * Return MySQL connection parameter.
     *
     * @param  string     $param_name
     * @param  string|int $default
     * @return string|int
     */
    private function getTestMySqlConnectionParam($param_name, $default)
    {
        $env_variable_name = $this->getEnvVariablePrefix() . 'MYSQL_TEST_' . strtoupper($param_name);

        return getenv($env_variable_name) ? getenv($env_variable_name) : $default;
    }

    /**
     * Return test database name.
     *
     * @param  string $app_name
     * @return string
     */
    protected function getTestMySqlDatabaseName($app_name)
    {
        return Inflector::tableize($app_name) . '_test';
    }
}
