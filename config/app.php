<?php
/**
 * Main application configuration.
 *
 * All paths are relative to basePath without the leading /, unless indicated otherwise.
 *
 * @author Miša Brežanac <brezanac@gmail.com>
 */

return [
    'app' => [
        /**
         * Application base path.
         */
        'basePath' => realpath(dirname(__FILE__, 2)),

        /**
         * Active timezone.
         */
        'timezone' => 'Europe/Berlin',

        /**
         * Display errors.
         */
        'displayErrors' => true,

        /**
         * Error reporting level.
         */
        'errorReporting' => E_ALL & ~E_NOTICE,

        /**
         * Environment configuration.
         */
        'env' => [
            /**
             * List of environments that are allowed for the application.
             */
            'allowedEnvironments' => ['development', 'production']
        ],

        /**
         * Logger (Monolog) configuration.
         */
        'log' => [
            /**
             * Logger name (used to identify logs in the log files).
             */
            'logName' => 'App',

            /**
             * Minimal severity level to log (DEBUG, INFO, NOTICE, WARNING, ERROR, CRITICAL, ALERT, EMERGENCY).
             */
            'logLevel' => 'INFO',

            /**
             * Path to the location where the log will be written.
             */
            'logPath' => 'storage/logs/app.log'
        ],

        /**
         * Twig related configuration.
         */
        'twig' => [
            /**
             * Path to Twig templates.
             */
            'templatesPath' => 'templates',

            /**
             * Path to Twig cache folder.
             */
            'cachePath' => 'storage/cache/twig',

            /**
             * Should Twig revision the static assets.
             * WARNING: If you disable revisioning after content
             * has been generated make sure to clear Twig cache!
             */
            'revisionAssets' => true,

            /**
             * Activate Twig debug mode.
             */
            'debugMode' => true,

            /**
             * Path to the static assets revision manifest.
             */
            'manifestPath' => 'storage/rev-manifest.json'
        ]
    ]
];
