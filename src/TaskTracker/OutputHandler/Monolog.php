<?php

namespace TaskTracker\OutputHandler;
use TaskTracker\Report;
use Monolog\Logger;

/**
 * Monolog output handler for TaskTracker
 */
class Monolog extends OutputHandler
{
    const EVERYTICK = 0;

    // --------------------------------------------------------------

    /**
     * @var Monolog\Logger
     */
    private $logger;

    /**
     * @var float  Microtime
     */
    private $logInterval;

    /**
     * @var float  Microtime
     */
    private $lastOutputTime = null;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param Monolog\Logger $logger
     * @param int $logInterval  Optional
     */
    public function __construct(Logger $logger, $logInterval = self::EVERYTICK)
    {
        $this->logger = $logger;
        $this->setLogInterval($logInterval);
    }

    // --------------------------------------------------------------

    /**
     * Set log interval so that Monolog does not generate a bazillion log entries
     *
     * Must be a positive integer
     *
     * @param int $seconds  Or use Monolog::EVERYTICK to disable delay
     */
    public function setLogInterval($seconds)
    {
        assert(is_int($seconds) && $seconds >= 0);
        $this->logInterval = (float) $seconds;
    }

    // --------------------------------------------------------------

    /** @inherit */
    public function tick(Report $report)
    {
        $now = microtime(true);

        //Check to see if we need to wait to do another report
        if ($this->logInterval > self::EVERYTICK && ! is_null($this->lastOutputTime)) {

            //If not enough time has passed, skip...
            if (($now - $this->lastOutputTime) < $this->logInterval) {
                return;
            }
        }

        $this->logger->addInfo($report->currMessage, (array) $report);
        $this->lastOutputTime = $now;
    }

    // --------------------------------------------------------------

    /** @inherit */
    public function abort(Report $report)
    {
        $this->logger->addError("Aborting. . . " . $report->currMessage, (array) $report);
    }

    // --------------------------------------------------------------

    /** @inherit */
    public function finish(Report $report)
    {
        $this->logger->addInfo("Finishing. . . " . $report->currMessage, (array) $report);
    } 
}

/* EOF: Monolog.php */