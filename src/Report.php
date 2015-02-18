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

    /**
     * @var float
     */
    private $itemTime;

    /**
     * @var float
     */
    private $maxTickTime;

    /**
     * @var float
     */
    private $minTickTime;

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

        // Also, determining item time values needs to happen immediately
        $this->item = ($this->tracker->getLastTick())
            ? $this->tick->getTimestamp() - $this->tracker->getLastTick()->getTimestamp()
            : $this->getTimeElapsed();

        if ($this->tracker->getLastTick()) {
            $this->minTickTime = min($this->getItemTime(), $this->tracker->getLastTick()->getReport()->getMinItemTime());
            $this->maxTickTime = max($this->getItemTime(), $this->tracker->getLastTick()->getReport()->getMaxItemTime());
        }
        else {
            $this->minTickTime = $this->minTickTime = $this->getItemTime();
            $this->maxTickTime = $this->minTickTime = $this->getItemTime();
        }
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
        return $this->tracker->getNumProcessedItems(Tick::SUCCESS);
    }

    /**
     * @return int
     */
    function getNumItemsFail()
    {
        return $this->tracker->getNumProcessedItems(Tick::FAIL);
    }

    /**
     * @return int
     */
    function getNumItemsSkip()
    {
        return $this->tracker->getNumProcessedItems(Tick::SKIP);
    }

    /**
     * @return float
     */
    function getItemTime()
    {
        return $this->itemTime;
    }

    /**
     * @return float
     */
    function getMaxItemTime()
    {
        return $this->maxTickTime;
    }

    /**
     * @return float
     */
    function getMinItemTime()
    {
        return $this->minTickTime;
    }

    /**
     * @return float
     */
    function getAvgItemTime()
    {
        return ($this->getNumItemsProcessed() > 0)
            ? ($this->getTimeElapsed() / $this->getNumItemsProcessed())
            : 0;
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
