<?php

namespace App\Exceptions;

use App\Core\Log;
use Monolog\Logger;

/**
 *
 * Router exception class.
 *
 * @author Miša Brežanac <brezanac@gmail.com>
 */
class RouterException extends \Exception
{
    /**
     * Requested route not found (basically a 404).
     */
    const ROUTE_NOT_FOUND = 1;

    /**
     * A route exists but specifies a different request method.
     */
    const ROUTE_METHOD_NOT_ALLOWED = 2;

    /**
     * @var int Severity level of the exception, tied to the Monolog levels.
     */
    private $severity = LOGGER::INFO;

    /**
     * @var array Exception context (additional data).
     */
    private $context;

    public function __construct($exceptionCode, $context = [])
    {
        parent::__construct($message = "", $code = $exceptionCode);
        $this->context = $context;

        switch ($exceptionCode) {
            case self::ROUTE_NOT_FOUND:
                $this->message = '[Router] HTTP 404 Not Found.';
                $this->severity = LOGGER::NOTICE;
                break;
            case self::ROUTE_METHOD_NOT_ALLOWED:
                $this->message = '[Router] HTTP 405 Method Not Allowed.';
                $this->severity = LOGGER::ERROR;
                break;
        }
    }

    /**
     * Returns the exception severity level.
     *
     * @return int Exception severity level.
     */
    public function getSeverity()
    {
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
