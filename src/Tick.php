<?php

namespace TaskTracker;

use InvalidArgumentException;
use Symfony\Component\EventDispatcher\Event;

/**
 * Represents a progress tick
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class Tick extends Event implements TickInterface
{
    const SUCCESS  = 1;
    const FAIL     = 0;
    const SKIP     = -1;
    
    /**
     * @var string  Custom Message
     */
    private $message;

    /**
     * @var float  Timestamp (microtime float)
     */
    private $timestamp;

    /**
     * @var int  The status of tick (success, fail, or skip)
     */
    private $status;

    /**
     * @var int
     */
    private $incrementBy;

    /**
     * @var Report
     */
    private $report;

    /**
     * @var array
     */
    private $extraInfo;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param Tracker $tracker
     * @param int     $status
     * @param string  $message
     * @param array   $extraInfo
     * @param int     $incrementBy
     */
    public function __construct(Tracker $tracker, $status = self::SUCCESS, $message = '', array $extraInfo = [], $incrementBy = 1)
    {
        if ( ! in_array($status, [self::FAIL, self::SKIP, self::SUCCESS])) {
            throw new InvalidArgumentException("Invalid tick status");
        }

        //Set parameters
        $this->incrementBy = $incrementBy;
        $this->status      = (int) $status;
        $this->timestamp   = microtime(true);
        $this->message     = $message;
        $this->extraInfo   = $extraInfo;
        $this->report      = new Report($this, $tracker);
    }

    // ---------------------------------------------------------------

    /**
     * Returns the message associated with the Tick event
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Returns the timestamp (microtime float) of the Tick event
     *
     * @return float
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Returns the status (Tick::SUCCESS, Tick::FAIL, TICK::SKIP) of the Tick event
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Returns the number of increments associated with with the Tick event
     *
     * @return int
     */
    public function getIncrementBy()
    {
        return $this->incrementBy;
    }

    /**
     * Returns the report associated with the Tick event
     *
     * @return Report
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * Returns any extra information associated with the Tick event
     *
     * @return array
     */
    public function getExtraInfo()
    {
        return $this->extraInfo;
    }
} 
