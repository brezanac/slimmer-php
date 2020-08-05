<?php

namespace App\Database;

use App\Core\Config;

/**
 * MySQL database connection.
 *
 * @author Miša Brežanac <brezanac@gmail.com>
 */
class Database
{
    /**
     * @var Database Connection instance.
     */
    private static $instance;

    private function __construct()
    {
    }

    // Cloning should also be prohibited.
    private function __clone()
    {

    }

    /**
     * Instantiates a connection or returns and existing one.
     *
     * @return mixed
     */
    public static function init()
    {
        if (!isset(self::$instance)) {
            self::createConnection();
        }

        return self::$instance;
    }

    /**
     * Creates a connection to the database.
     */
    private static function createConnection()
    {
        $settings = Config::get('database');
        $dsn = sprintf("mysql:host=%s;port=%s;dbname=%s;charset=%s", $settings['host'], $settings['port'], $settings['name'], $settings['charset']);

        try {
            self::$instance = new \PDO($dsn, $settings['user'], $settings['password'], $settings['options']);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }
    }
}
