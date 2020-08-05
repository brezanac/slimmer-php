<?php

namespace App\Exceptions;

use App\Core\Log;
use Monolog\Logger;

/**
 *
 * Config exception class.
 *
 * @author Miša Brežanac <brezanac@gmail.com>
 */
class ConfigException extends \Exception
{
    /**
     * Missing environmental configuration.
     */
    const MISSING_ENV = 1;

    /**
     * Invalid configuration environment was supplied.
     */
    const INVALID_ENVIRONMENT = 2;

    /**
     * No custom environment is supplied.
     */
    const MISSING_ENVIRONMENT = 3;

    /**
     * @var int Severity level of the exception, tied to the Monolog levels.
     */
    protected $severity = LOGGER::INFO;

    /**
     * @var array Exception context (additional data).
     */
    private $context;

    public function __construct($exceptionCode, $context = [])
    {
        parent::__construct($message = "", $code = $exceptionCode);
        $this->context = $context;

        switch ($exceptionCode) {
            case self::MISSING_ENV:
                $this->message = '[Config] Skipping import of environmental variables due to missing .env file.';
                $this->severity = LOGGER::NOTICE;
                break;
            case self::INVALID_ENVIRONMENT:
                $this->message = '[Config] Invalid environment requested.';
                $this->severity = LOGGER::ERROR;
                break;
            case self::MISSING_ENVIRONMENT:
                $this->message = '[Config] No environment supplied (missing APP_ENV environmental variable).';
                $this->severity = LOGGER::NOTICE;
                break;
        }
    }

    /**
     * Returns the exception severity level.
     *
     * @return int Exception severity level.
     */
    public function getSeverity() {
        return $this->severity;
    }

    /**
     * @return mixed Returns the supplied exception context.
     */
    public function getContext()
    {
        return $this->context;
    }
}


