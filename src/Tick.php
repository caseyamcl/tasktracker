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

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param Tracker $tracker
     * @param int     $status
     * @param string  $message
     * @param int     $incrementBy
     */
    public function __construct(Tracker $tracker, $status = self::SUCCESS, $message = '', $incrementBy = 1)
    {
        if ( ! in_array($status, [self::FAIL, self::SKIP, self::SUCCESS])) {
            throw new InvalidArgumentException("Invalid tick status");
        }

        //Set parameters
        $this->incrementBy = $incrementBy;
        $this->status      = (int) $status;
        $this->timestamp   = microtime(true);
        $this->message     = $message;
        $this->report      = new Report($this, $tracker);
    }

    // ---------------------------------------------------------------

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Get timestamp (microtime float)
     * @return float
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get incrementBy (in numbers)
     *
     * @return int
     */
    public function getIncrementBy()
    {
        return $this->incrementBy;
    }

    /**
     * @return Report
     */
    public function getReport()
    {
        return $this->report;
    }
} 

/* EOF: Tick.php */
