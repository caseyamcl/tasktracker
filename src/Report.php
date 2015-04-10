<?php

/**
 * Tack Tracker - A library for tracking long-running task progress
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/caseyamcl/tasktracker
 * @version 2.0
 * @package caseyamcl/tasktracker
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * ------------------------------------------------------------------
 */

namespace TaskTracker;

use TaskTracker\Helper\MagicPropsTrait;

/**
 * Task Tracker Report
 *
 * Represents a snapshot of task state from a specific time*
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
        $this->itemTime = ($this->tracker->getLastTick())
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
     * Returns the time this task started in microseconds
     *
     * @return float
     */
    public function getTimeStarted()
    {
        return $this->tracker->getStartTime();
    }

    /**
     * Returns the total number of items that are to be processed.
     *
     * If unknown or not specified, this returns Tracker::UNKNOWN
     *
     * @return int
     */
    public function getTotalItemCount()
    {
        return $this->tracker->getNumTotalItems();
    }

    /**
     * Get the Tracker Tick object for this report
     *
     * @return Tick
     */
    public function getTick()
    {
        return $this->tick;
    }

    /**
     * Returns the total number of items processed (including skipped and failed)
     *
     * @return int
     */
    public function getNumItemsProcessed()
    {
        return $this->tracker->getNumProcessedItems();
    }

    /**
     * Returns the time elapsed in microseconds
     *
     * @return float
     */
    public function getTimeElapsed()
    {
        return $this->tick->getTimestamp() - $this->tracker->getStartTime();
    }


    /**
     * Returns the number of items thus far that successfully processed
     *
     * @return int
     */
    public function getNumItemsSuccess()
    {
        return $this->tracker->getNumProcessedItems(Tick::SUCCESS);
    }

    /**
     * Returns the number of items processed thus far that failed
     *
     * @return int
     */
    public function getNumItemsFail()
    {
        return $this->tracker->getNumProcessedItems(Tick::FAIL);
    }

    /**
     * Returns the number of items processed thus far that were skipped
     *
     * @return int
     */
    public function getNumItemsSkip()
    {
        return $this->tracker->getNumProcessedItems(Tick::SKIP);
    }

    /**
     * Returns the amount of time the last item took to process
     *
     * @return float
     */
    public function getItemTime()
    {
        return $this->itemTime;
    }

    /**
     * Returns the maximum amount of time any one item has taken to process thus far
     *
     * @return float
     */
    public function getMaxItemTime()
    {
        return $this->maxTickTime;
    }

    /**
     * Returns the minimum amount of time any one item has taken to process thus far
     *
     * @return float
     */
    public function getMinItemTime()
    {
        return $this->minTickTime;
    }

    /**
     * Returns the current average (mean) amount of time that items have taken to process thus far
     *
     * @return float
     */
    public function getAvgItemTime()
    {
        return ($this->getNumItemsProcessed() > 0)
            ? ($this->getTimeElapsed() / $this->getNumItemsProcessed())
            : 0;
    }

    /**
     * Returns the message associated with the last Tick event
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->tick->getMessage();
    }

    /**
     * Returns the timestamp (microtime float) for this Tick event
     *
     * @return float
     */
    public function getTimestamp()
    {
        return $this->tick->getTimestamp();
    }

    /**
     * Returns the status (Tick::SUCCESS, Tick::FAIL, TICK::SKIP) of the last item processed
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->tick->getStatus();
    }

    /**
     * Returns the number of increments associated with the last processed item
     *
     * @return int
     */
    public function getIncrementBy()
    {
        return $this->tick->getIncrementBy();
    }

    /**
     * Returns the memory usage at the time of the last processed item (in bytes)
     *
     * @return int
     */
    public function getMemUsage()
    {
        return $this->memUsage;
    }

    /**
     * Returns the peak memory usage thus far
     *
     * @return int
     */
    public function getMemPeakUsage()
    {
        return $this->memPeakUsage;
    }

    /**
     * Returns this report
     *
     * @return Report
     */
    public function getReport()
    {
        return $this;
    }

    /**
     * Returns any extra information associated with the last tick
     *
     * @return array
     */
    public function getExtraInfo()
    {
        return $this->tick->getExtraInfo();
    }

    // ---------------------------------------------------------------

    /**
     * Converts this report to an array
     *
     * @return array
     */
    public function toArray()
    {
        $arr = $this->traitToArray();
        unset($arr['report'], $arr['tick']);
        return $arr;
    }
}
