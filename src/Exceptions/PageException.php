<?php

namespace App\Exceptions;

use Monolog\Logger;
use App\Content\Page;

/**
 *
 * Page exception class.
 *
 * @author Miša Brežanac <brezanac@gmail.com>
 */
class PageException extends \Exception
{
    /**
     * The manifest file with all the static asset mappings is missing.
     */
    const MISSING_MANIFEST_FILE = 1;

    /**
     * The requested page does not exist.
     */
    const PAGE_NOT_FOUND = 2;

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
            case self::MISSING_MANIFEST_FILE:
                $this->message = '[Page] Missing manifest file.';
                $this->severity = LOGGER::NOTICE;
                break;
            case self::PAGE_NOT_FOUND:
                $this->message = '[Page] Page not found.';
                $this->severity = LOGGER::NOTICE;
                Page::show404();
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
