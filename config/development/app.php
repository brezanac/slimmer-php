<?php
/**
 * Development specific main application settings.
 *
 * All paths are relative to basePath without the leading /, unless indicated otherwise.
 *
 * @author Miša Brežanac <brezanac@gmail.com>
 */

return [
    'app' => [
        /**
         * Display errors.
         */
        'display_errors' => true,

        /**
         * Error reporting level.
         */
        'errorReporting' => E_ALL,

        /**
         * Logger (Monolog) configuration.
         */
        'log' => [
            /**
             * Minimal severity level to log (DEBUG, INFO, NOTICE, WARNING, ERROR, CRITICAL, ALERT, EMERGENCY).
             */
            'logLevel' => 'DEBUG'
        ]
    ]
];
