<?php
/**
 * Database configuration.
 *
 * @author Miša Brežanac <brezanac@gmail.com>
 */

return [
    /*
     * Database connection settings.
     */
    'database' => [
        /**
         * If set to true a database connection will be automatically initiated on every request.
         * If set to false a database connection will have to be manually created with Database::init().
         * This option is here out of performance reasons because if the application does not need
         * access to a database on every request, setting autoInit to false will have a noticeable
         * effect on the application performance.
         */
        'autoInit' => false,

        /*
         * Database connection settings.
         *
         * NOTE: All connection settings are expected to be given in the main .env file.
         * Unless you specify them there you won't be able to use database connections!
         */
        'host' => isset($_ENV['DB_HOST']) ? $_ENV['DB_HOST'] : '',
        'port' => isset($_ENV['DB_PORT']) ? $_ENV['DB_PORT'] : '',
        'name' => isset($_ENV['DB_NAME']) ? $_ENV['DB_NAME'] : '',
        'user' => isset($_ENV['DB_USER']) ? $_ENV['DB_USER'] : '',
        'password' => isset($_ENV['DB_PASSWORD']) ? $_ENV['DB_PASSWORD'] : '',
        'charset' => isset($_ENV['DB_CHARSET']) ? $_ENV['DB_CHARSET'] : '',

        // Options specific to PDO.
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    ]
];

