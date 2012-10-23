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
    public function tick(Report $report, $msg)
    {
        $now = microtime(true);

        //Check to see if we need to wait to do another report
        if ($this->logInterval > self::EVERYTICK && ! is_null($this->lastOutputTime)) {

            //If not enough time has passed, skip...
            if (($now - $this->lastOutputTime) < $this->logInterval) {
                return;
            }
        }

        $this->logger->addInfo($msg, $report->toArray());
        $this->lastOutputTime = $now;
    }

    // --------------------------------------------------------------

    /** @inherit */
    public function start(Report $report, $msg)
    {
        $this->logger->addInfo("Starting. . . " . $msg, $report->toArray());
    }

    // --------------------------------------------------------------

    /** @inherit */
    public function abort(Report $report, $msg)
    {
        $this->logger->addError("Aborting. . . " . $msg, $report->toArray());
    }

    // --------------------------------------------------------------

    /** @inherit */
    public function finish(Report $report, $msg)
    {
        $this->logger->addInfo("Finishing. . . " . $msg, $report->toArray());
    } 
}

/* EOF: Monolog.php */