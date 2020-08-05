<?php

namespace App\Core;

use App\Database\Database;

/**
 * Initiates the application and starts the party.
 *
 * @author Miša Brežanac <brezanac@gmail.com>
 */
class Bootstrap
{

    public function __construct() {

    }

    public function run()
    {
        // Load main configuration.
        $settings = Config::load('../config', '../');

        // Setting the default timezone.
        date_default_timezone_set($settings['app']['timezone']);

        // Setting up error reporting.
        ini_set('display_errors', $settings['app']['displayErrors']);
        error_reporting($settings['app']['errorReporting']);

        // Initialize logging.
        Log::init();

        /*
         * Automatically initialize database connection but ONLY if the app is configured to do so.
         */
        if ($settings['database']['autoInit']) {
            Database::init();
        }

        // Initialize the router.
        $router = new Router();
        $router->init();
    }
}
