<?php

namespace TaskTracker;

use TaskTracker\Helper\MagicPropsTrait;

/**
 * Report Value Object
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class Report implements ReportInterface
{
    use MagicPropsTrait {
        toArray as traitToArray;
    }

    // --------------------------------------------------------------

    /**
     * @var Tick
     */
    private $tick;

    /**
     * @var Tracker
     */
    private $tracker;

    /**
     * @var int
     */
    private $memUsage;

    /**
     * @var int
     */
    private $memPeakUsage;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param Tick      $tick     Empty if no tick yet
     * @param Tracker   $tracker  The Task Tracker
     */
    public function __construct(Tick $tick, Tracker $tracker)
    {
        $this->tick    = $tick;
        $this->tracker = $tracker;

        // A snapshot of these values needs to be created upon report generation
        $this->memUsage     = memory_get_usage(true);
        $this->memPeakUsage = memory_get_peak_usage(true);
    }

    // ---------------------------------------------------------------

    /**
     * @return float
     */
    function getTimeStarted()
    {
        return $this->tracker->getStartTime();
    }

    /**
     * @return int
     */
    function getTotalItemCount()
    {
        return $this->tracker->getNumTotalItems();
    }

    /**
     * @return Tick
     */
    function getTick()
    {
        return $this->tick;
    }

    /**
     * @return int
     */
    function getNumItemsProcessed()
    {
        return $this->tracker->getNumProcessedItems();
    }

    /**
     * @return float
     */
    function getTimeElapsed()
    {
        return $this->tick->getTimestamp() - $this->tracker->getStartTime();
    }


    /**
     * @return int
     */
    function getNumItemsSuccess()
    {
        return $this->getNumItemsProcessed(Tick::SUCCESS);
    }

    /**
     * @return int
     */
    function getNumItemsFail()
    {
        return $this->getNumItemsProcessed(Tick::FAIL);
    }

    /**
     * @return int
     */
    function getNumItemsSkip()
    {
        return $this->getNumItemsProcessed(Tick::SKIP);
    }

    /**
     * @return float
     */
    function getItemTime()
    {
        return ($this->tracker->getLastTick())
            ? $this->tick->getTimestamp() - $this->tracker->getLastTick()->getTimestamp()
            : $this->getTimeElapsed();
    }

    /**
     * @return float
     */
    function getMaxItemTime()
    {
        $lastTime = $this->tracker->getLastTick()
            ? $this->tracker->getLastTick()->getReport()->getMaxItemTime()
            : $this->getItemTime();

        return max($this->getItemTime(), $lastTime);
    }

    /**
     * @return float
     */
    function getMinItemTime()
    {
        $lastTime = $this->tracker->getLastTick()
            ? $this->tracker->getLastTick()->getReport()->getMaxItemTime()
            : $this->getItemTime();

        return min($this->getItemTime(), $lastTime);
    }

    /**
     * @return float
     */
    function getAvgItemTime()
    {
        return $this->getTimeElapsed() / $this->getNumItemsProcessed();
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->tick->getMessage();
    }

    /**
     * Get timestamp (microtime float)
     *
     * @return float
     */
    public function getTimestamp()
    {
        return $this->tick->getTimestamp();
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->tick->getStatus();
    }

    /**
     * Get incrementBy (in numbers)
     *
     * @return int
     */
    public function getIncrementBy()
    {
        return $this->tick->getIncrementBy();
    }

    /**
     * Get memory usage at time of tick (in bytes)
     *
     * @return int
     */
    public function getMemUsage()
    {
        return $this->memUsage;
    }

    /**
     * @return int
     */
    function getMemPeakUsage()
    {
        return $this->memPeakUsage;
    }

    /**
     * @return Report
     */
    public function getReport()
    {
        return $this;
    }

    /**
     * @return array
     */
    public function getExtraInfo()
    {
        return $this->tick->getExtraInfo();
    }

    // ---------------------------------------------------------------

    /**
     * @return array
     */
    public function toArray()
    {
        $arr = $this->traitToArray();
        unset($arr['report'], $arr['tick']);
        return $arr;
    }
}
