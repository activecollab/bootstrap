<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Bootstrap\TestCase;

use ActiveCollab\DatabaseConnection\Connection\MysqliConnection;
use ActiveCollab\DatabaseConnection\ConnectionInterface;
use Doctrine\Common\Inflector\Inflector;
use mysqli;
use RuntimeException;

/**
 * @package ActiveCollab\Id\Test
 */
abstract class DatabaseTestCase extends TestCase
{
    /**
     * @var mysqli
     */
    private $link;

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * Tear down test environment.
     */
    public function tearDown()
    {
        if ($this->connection) {
            $this->connection->execute('SET foreign_key_checks = 0;');
            foreach ($this->connection->getTableNames() as $table_name) {
                $this->connection->dropTable($table_name);
            }
            $this->connection->execute('SET foreign_key_checks = 1;');

            $this->connection = null;
        }

        if ($this->link) {
            $this->link->close();
        }

        parent::tearDown();
    }

    /**
     * Return database connection link.
     *
     * @return mysqli
     */
    protected function &getDatabaseLink()
    {
        if (empty($this->link)) {
            $db_host = $this->getTestMySqlHost();
            $db_port = $this->getTestMySqlPort();
            $db_user = $this->getTestMySqlUser();
            $db_pass = $this->getTestMySqlPassword();
            $db_name = $this->getTestMySqlDatabase();

            $this->link = new \MySQLi("$db_host:$db_port", $db_user, $db_pass);

            if ($this->link->connect_error) {
                throw new RuntimeException('Failed to connect to database. MySQL said: ' . $this->link->connect_error);
            }

            if (!$this->link->select_db($db_name)) {
                throw new RuntimeException('Failed to select database');
            }
        }

        return $this->link;
    }

    /**
     * Return database connection instance.
     *
     * @return ConnectionInterface
     */
    protected function &getDatabaseConnection()
    {
        if (empty($this->connection)) {
            $this->connection = new MysqliConnection($this->getDatabaseLink());

            $this->connection->execute('SET foreign_key_checks = 0;');
            foreach ($this->connection->getTableNames() as $table_name) {
                $this->connection->dropTable($table_name);
            }
            $this->connection->execute('SET foreign_key_checks = 1;');
        }

        return $this->connection;
    }

    /**
     * @return string
     */
    protected function getTestMySqlHost()
    {
        return $this->getTestMySqlConnectionParam('host', 'localhost');
    }

    /**
     * @return string
     */
    protected function getTestMySqlPort()
    {
        return $this->getTestMySqlConnectionParam('port', 3306);
    }

    /**
     * @return string
     */
    protected function getTestMySqlUser()
    {
        return $this->getTestMySqlConnectionParam('user', 'root');
    }

    /**
     * @return string
     */
    protected function getTestMySqlPassword()
    {
        return $this->getTestMySqlConnectionParam('pass', '');
    }

    /**
     * @return string
     */
    protected function getTestMySqlDatabase()
    {
        return $this->getTestMySqlConnectionParam('database', Inflector::tableize($this->getAppName()) . '_test');
    }

    /**
     * Cached MySQL connection parameters.
     *
     * @var string[]|int[]
     */
    private $test_mysql_connection_params = [];

    /**
     * Return MySQL connection parameter.
     *
     * @param  string     $param_name
     * @param  string|int $default
     * @return string|int
     */
    private function getTestMySqlConnectionParam($param_name, $default)
    {
        if (!array_key_exists($param_name, $this->test_mysql_connection_params)) {
            $env_variable_name = $this->getEnvVariablePrefix() . 'MYSQL_TEST_' . strtoupper($param_name);

            $this->test_mysql_connection_params[$param_name] = getenv($env_variable_name) ? getenv($env_variable_name) : $default;
        }

        return $this->test_mysql_connection_params[$param_name];
    }
}
