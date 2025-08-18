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

declare(strict_types=1);

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

    private Tick $tick;
    private Tracker $tracker;
    private int $memUsage;
    private int $memPeakUsage;
    private float $itemTime;
    private float $maxTickTime;
    private float $minTickTime;

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
            $this->maxTickTime = $this->minTickTime = $this->getItemTime();
        }
    }

    /**
     * Returns the time this task started in microseconds
     */
    public function getTimeStarted(): float
    {
        return $this->tracker->getStartTime();
    }

    /**
     * Returns the total number of items that are to be processed.
     *
     * If unknown or not specified, this returns Tracker::UNKNOWN
     */
    public function getTotalItemCount(): int
    {
        return $this->tracker->getNumTotalItems();
    }

    /**
     * Get the Tracker Tick object for this report
     */
    public function getTick(): Tick
    {
        return $this->tick;
    }

    /**
     * Returns the total number of items processed (including skipped and failed)
     */
    public function getNumItemsProcessed(): int
    {
        return $this->tracker->getNumProcessedItems();
    }

    /**
     * Returns the time elapsed in microseconds
     */
    public function getTimeElapsed(): float
    {
        return $this->tick->getTimestamp() - $this->tracker->getStartTime();
    }


    /**
     * Returns the number of items thus far that successfully processed
     */
    public function getNumItemsSuccess(): int
    {
        return $this->tracker->getNumProcessedItems(Tick::SUCCESS);
    }

    /**
     * Returns the number of items processed thus far that failed
     */
    public function getNumItemsFail(): int
    {
        return $this->tracker->getNumProcessedItems(Tick::FAIL);
    }

    /**
     * Returns the number of items processed thus far that were skipped
     */
    public function getNumItemsSkip(): int
    {
        return $this->tracker->getNumProcessedItems(Tick::SKIP);
    }

    /**
     * Returns the amount of time the last item took to process
     */
    public function getItemTime(): float
    {
        return $this->itemTime;
    }

    /**
     * Returns the maximum amount of time any one item has taken to process thus far
     */
    public function getMaxItemTime(): float
    {
        return $this->maxTickTime;
    }

    /**
     * Returns the minimum amount of time any one item has taken to process thus far
     */
    public function getMinItemTime(): float
    {
        return $this->minTickTime;
    }

    /**
     * Returns the current average (mean) amount of time that items have taken to process thus far
     */
    public function getAvgItemTime(): float
    {
        return ($this->getNumItemsProcessed() > 0)
            ? ($this->getTimeElapsed() / $this->getNumItemsProcessed())
            : 0;
    }

    /**
     * Returns the message associated with the last Tick event
     */
    public function getMessage(): string
    {
        return $this->tick->getMessage();
    }

    /**
     * Returns the timestamp (microtime float) for this Tick event
     */
    public function getTimestamp(): float
    {
        return $this->tick->getTimestamp();
    }

    /**
     * Returns the status (Tick::SUCCESS, Tick::FAIL, TICK::SKIP) of the last item processed
     */
    public function getStatus(): int
    {
        return $this->tick->getStatus();
    }

    /**
     * Returns the number of increments associated with the last processed item
     */
    public function getIncrementBy(): int
    {
        return $this->tick->getIncrementBy();
    }

    /**
     * Returns the memory usage at the time of the last processed item (in bytes)
     */
    public function getMemUsage(): int
    {
        return $this->memUsage;
    }

    /**
     * Returns the peak memory usage thus far
     */
    public function getMemPeakUsage(): int
    {
        return $this->memPeakUsage;
    }

    /**
     * Returns this report
     */
    public function getReport(): Report
    {
        return $this;
    }

    /**
     * Returns any extra information associated with the last tick
     */
    public function getExtraInfo(): array
    {
        return $this->tick->getExtraInfo();
    }

    /**
     * Converts this report to an array
     */
    public function toArray(): array
    {
        $arr = $this->traitToArray();
        unset($arr['report'], $arr['tick']);
        return $arr;
    }
}
