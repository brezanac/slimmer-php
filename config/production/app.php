<?php
/**
 * Production specific main application settings.
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
        'display_errors' => false,

        /**
         * Error reporting level.
         */
        'errorReporting' => 0,

        /**
         * Logger (Monolog) configuration.
         */
        'log' => [
            /**
             * Minimal severity level to log (DEBUG, INFO, NOTICE, WARNING, ERROR, CRITICAL, ALERT, EMERGENCY).
             */
            'logLevel' => 'WARNING'
        ],

        /**
         * Twig related configuration.
         */
        'twig' => [
            /**
             * Activate Twig debug mode.
             */
            'debugMode' => false,
        ]
    ]
];
