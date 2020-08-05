<?php

namespace App\Core;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * A Monolog based Logger.
 *
 * @author Miša Brežanac <brezanac@gmail.com>
 * @method addRecord(string $level, $message, array $context)
 */
class Log
{
    /**
     * @var Log Logger instance.
     */
    private static $instance;

    /**
     * @var array Default context to log every time.
     */
    private static $defaultContext = [];

    // No direct instantiation of Logging.
    private function __construct()
    {
    }

    // Cloning should also be prohibited.
    private function __clone()
    {
    }

    /**
     * Creates the logger instance if required and returns it.
     *
     * @return Log The logger instance.
     */
    public static function init()
    {
        if (!isset(self::$instance)) {
            self::createLogger();
        }

        return self::$instance;
    }

    /**
     * Instantiates and initializes a logger.
     */
    private static function createLogger()
    {
        $config = Config::get('app');
        $path = sprintf('%s/%s', $config['basePath'], $config['log']['logPath']);

        self::$instance = new Logger($config['log']['logName']);
        self::$instance->pushHandler(new StreamHandler($path, constant(sprintf("Monolog\Logger::%s", $config['log']['logLevel']))));

        // Adding default context to every logged message.
        self::$defaultContext = [
            // Log the actual request URI every time.
            'URI' => $_SERVER['REQUEST_URI']
        ];
    }

    /**
     * Generic log function with ability to specify the logging level.
     *
     * @param string $message The message to be logged.
     * @param int $level Log level message (info, warning etc.)
     * @param array $context Additional information stored in the log entry.
     */
    public static function log(string $message, int $level = LOGGER::INFO, array $context = [])
    {
        self::init()->addRecord($level, $message, array_merge(self::$defaultContext, $context));
    }

    /**
     * Logs a DEBUG level severity log entry.
     *
     * @param string $message The message to be logged.
     * @param array $context Additional information stored in the log entry.
     */
    public static function debug(string $message, array $context = [])
    {
        self::init()->addRecord(LOGGER::DEBUG, $message, array_merge(self::$defaultContext, $context));
    }

    /**
     * Logs an INFO level severity log entry.
     *
     * @param string $message The message to be logged.
     * @param array $context Additional information stored in the log entry.
     */
    public static function info(string $message, array $context = [])
    {
        self::init()->addRecord(LOGGER::INFO, $message, array_merge(self::$defaultContext, $context));
    }

    /**
     * Logs a NOTICE level severity log entry.
     *
     * @param string $message The message to be logged.
     * @param array $context Additional information stored in the log entry.
     */
    public static function notice(string $message, array $context = [])
    {
        self::init()->addRecord(LOGGER::NOTICE, $message, array_merge(self::$defaultContext, $context));
    }

    /**
     * Logs a WARNING level severity log entry.
     *
     * @param string $message The message to be logged.
     * @param array $context Additional information stored in the log entry.
     */
    public static function warning($message, $context = [])
    {
        self::init()->addRecord(LOGGER::WARNING, $message, array_merge(self::$defaultContext, $context));
    }

    /**
     * Logs an ERROR level severity log entry.
     *
     * @param string $message The message to be logged.
     * @param array $context Additional information stored in the log entry.
     */
    public static function error($message, $context = [])
    {
        self::init()->addRecord(LOGGER::ERROR, $message, array_merge(self::$defaultContext, $context));
    }

    /**
     * Logs a CRITICAL level severity log entry.
     *
     * @param string $message The message to be logged.
     * @param array $context Additional information stored in the log entry.
     */
    public static function critical($message, $context = [])
    {
        self::init()->addRecord(LOGGER::CRITICAL, $message, array_merge(self::$defaultContext, $context));
    }

    /**
     * Logs an ALERT level severity log entry.
     *
     * @param string $message The message to be logged.
     * @param array $context Additional information stored in the log entry.
     */
    public static function alert($message, $context = [])
    {
        self::init()->addRecord(LOGGER::ALERT, $message, array_merge(self::$defaultContext, $context));
    }

    /**
     * Logs an EMERGENCY level severity log entry.
     *
     * @param string $message The message to be logged.
     * @param array $context Additional information stored in the log entry.
     */
    public static function emergency($message, $context = [])
    {
        self::init()->addRecord(LOGGER::EMERGENCY, $message, array_merge(self::$defaultContext, $context));
    }
}
