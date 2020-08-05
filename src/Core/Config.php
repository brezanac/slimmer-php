<?php

namespace App\Core;

use Dotenv;
use App\Exceptions\ConfigException;

/**
 * A very simple persistent configuration container.
 *
 * @author Miša Brežanac <brezanac@gmail.com>
 */
class Config
{
    /**
     * @var Config The actual Config singleton instance.
     */
    private static $instance;

    /**
     * @var array List of all available environments.
     */
    private static $env = [];

    /**
     * @var string Name of the active environment.
     */
    private static $activeEnvironment = '';

    /**
     * @var array The actual configuration.
     */
    protected static $config = [];

    // Making sure Config is not publicly available for instantiating.
    private function __construct()
    {
    }

    // Cloning should also be prohibited.
    private function __clone()
    {

    }

    /**
     * Loads the main configuration.
     *
     * @param $path string Path to the config folder.
     * @param $envPath string Location of the DotEnv file.
     * @return array Loaded settings.
     */
    public static function load($path, $envPath)
    {
        if (!isset(self::$instance)) {
            self::$instance = new static;
        }

        // DotEnv mutability will provide overwriting of existing environmental variables.
        $dotenv = Dotenv\Dotenv::createMutable($envPath);
        $dotenv->load();

        // Initial load of the default configuration.
        $configs = new \DirectoryIterator(realpath($path));
        foreach ($configs as $fileInfo) {
            $fileName = $fileInfo->getFilename();
            $filePath = $fileInfo->getPathname();

            if ($fileInfo->isDot()) {
                continue;
            }

            // Taking note of all available environments.
            if ($fileInfo->isDir()) {
                self::$env[] = $fileName;
                continue;
            }

            self::$config = array_merge_recursive(self::$config, require "$filePath");
        }

        $settings = Config::get('app');

        // Load .env configuration if it exists and overload existing configuration with environment specific one.
        try {
            $dotenvPath = sprintf('%s/.env', $settings['basePath']);
            if (!is_file($dotenvPath)) {
                throw new ConfigException(ConfigException::MISSING_ENV, [
                    'dotenvPath' => $dotenvPath
                ]);
            }

            // No custom environment was supplied.
            if (!isset($_ENV['APP_ENV'])) {
                throw new ConfigException(ConfigException::MISSING_ENVIRONMENT);
            }

            // Invalid environment was requested.
            if (!in_array($_ENV['APP_ENV'], $settings['env']['allowedEnvironments'])) {
                throw new ConfigException(ConfigException::INVALID_ENVIRONMENT, [
                    'dotenvPath' => $dotenvPath,
                    'availableEnvironments' => self::$env,
                    'requestedEnvironment' => $_ENV['APP_ENV']
                ]);
            }

            self::$activeEnvironment = $_ENV['APP_ENV'];

            // Overloading the initially loaded configuration with the environment specific one.
            $envConfigs = new \DirectoryIterator(realpath(sprintf('%s/%s', $path, self::$activeEnvironment)));
            foreach ($envConfigs as $fileInfo) {
                $filePath = $fileInfo->getPathname();

                if ($fileInfo->isDot()) {
                    continue;
                }

                self::$config = array_replace_recursive(self::$config, require "$filePath");
            }
        } catch (ConfigException $e) {
            Log::log($e->getMessage(), $e->getSeverity(), $e->getContext());
        }

        return self::$config;
    }

    /**
     * Gets a configuration value.
     *
     * @param $key string Name of the configuration key to retrieve.
     * @return bool|mixed The value of the requested key.
     */
    public static function get($key)
    {
        if (!isset(self::$config[$key])) {
            return false;
        }

        return self::$config[$key];
    }

    /**
     * Sets a configuration value.
     *
     * @param $key string Name of the configuration key to set.
     * @param $value mixed Value to set for the key.
     */
    public static function set(string $key, $value)
    {
        // Existing keys will be just overwritten.
        self::$config[$key] = $value;
    }

    /**
     * Removes a configuration key.
     *
     * @param $key string Name of the key to remove.
     * @return bool|mixed Removal status.
     */
    public static function remove(string $key)
    {
        if (!isset(self::$config[$key])) {
            return false;
        }

        unset(self::$config[$key]);
        return true;
    }


    /**
     * Dumps the entire configuration.
     */
    public static function dump(): array
    {
        return self::$config;
    }

    /**
     * Returns the name of the active environment.
     */
    public static function getEnvironment()
    {
        if (isset(self::$activeEnvironment)) {
            return self::$activeEnvironment;
        }

        return false;
    }
}
