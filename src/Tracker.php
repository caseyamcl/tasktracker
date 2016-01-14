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

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Task Tracker
 *
 * Maintains the state of a long-running task and dispatches events
 * to subscribers for reporting on progress
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class Tracker
{
    const UNKNOWN = -1;

    const NOT_STARTED  = 0;
    const RUNNING      = 1;
    const FINISHED     = 2;
    const ABORTED      = 3;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var int  The number of total item (-1 for infinite/unknown)
     */
    private $numTotalItems;

    /**
     * @var array  Array holding processed items by Tick type
     */
    private $numProcessedItems;

    /**
     * @var float  The start time (a float value)
     */
    private $startTime;

    /**
     * @var Tick  Holds the last tick
     */
    private $lastTick;

    /**
     * @var int  One of the constants defined above
     */
    private $status;

    /**
     * Build a task tracker using a list of subscribers
     *
     * This is an alternate constructor, to be used when constructing a
     * Tracker object using a collection of subscribers
     *
     * @param array|EventSubscriberInterface[] $subscribers
     * @param int                              $totalItems
     * @return static
     */
    public static function build(array $subscribers = [], $totalItems = self::UNKNOWN)
    {
        // New object
        $that = new static($totalItems);

        // Register subscribers
        foreach ($subscribers as $subscriber) {
            $that->addSubscriber($subscriber);
        }

        // Return it
        return $that;
    }

    /**
     * Constructor
     *
     * @param EventDispatcherInterface  $dispatcher
     * @param int                       $totalItems     Default is unknown (-1)
     */
    public function __construct($totalItems = self::UNKNOWN, EventDispatcherInterface $dispatcher = null)
    {
        $this->status            = self::NOT_STARTED;
        $this->dispatcher        = $dispatcher ?: new EventDispatcher();
        $this->numTotalItems     = $totalItems;
        $this->numProcessedItems = [];
    }

    /**
     * Run as closure
     *
     * @param \Traversable $items
     * @param callable     $itemCallback  Callback accepts arguments: (Tracker $tracker, $item)
     * @return Report      The final report
     */
    public function run(\Traversable $items, callable $itemCallback)
    {
        foreach ($items as $item) {
            call_user_func($itemCallback, $this, $item);
        }

        return $this->getLastTick()->getReport();
    }


    /**
     * @return EventDispatcherInterface
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * Add a subscriber to this Tracker instance
     *
     * @param EventSubscriberInterface $subscriber
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->dispatcher->addSubscriber($subscriber);
    }

    /**
     * Get number of total items to be processed (-1 for unknown)
     *
     * @return int
     */
    public function getNumTotalItems()
    {
        return $this->numTotalItems;
    }

    /**
     * Return the number of items processed, including failed/succeeded
     *
     * @param int $tickType  Tick::SUCCESS, Tick::FAIL, Tick::SKIP, or null for all
     * @return int
     */
    public function getNumProcessedItems($tickType = null)
    {
        if (null !== $tickType) {
            return (array_key_exists($tickType, $this->numProcessedItems))
                ? $this->numProcessedItems[$tickType]
                : 0;
        }
        else {
            return array_sum($this->numProcessedItems);
        }
    }

    /**
     * Get the start time in microseconds (returns NULL if not yet started)
     *
     * @return float|null
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Get the last report
     *
     * Returns null if not started
     *
     * @return Report|null
     */
    public function getLastTick()
    {
        return $this->lastTick;
    }

    /**
     * Get the status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Is the tracker running?
     *
     * @return bool
     */
    public function isRunning()
    {
        return ($this->getStatus() == self::RUNNING);
    }

    /**
     * Start processing
     *
     * If this method is not called explicitely, it will automatically
     * be called upon first tick
     *
     * @param string $msg Optional message to include
     * @param array  $extraInfo
     */
    public function start($msg = null, array $extraInfo = [])
    {
        if ($this->status != self::NOT_STARTED) {
            throw new TrackerException("Cannot start tracker that was already started");
        }

        $this->status = self::RUNNING;
        $this->startTime = microtime(true);

        $tick = new Tick($this, Tick::SUCCESS, $msg, $extraInfo, 0);
        $this->dispatcher->dispatch(Events::TRACKER_START, $tick);
        $this->lastTick = $tick;
    }

    /**
     * Indicate progress to the tracker
     *
     * Builds a report and send it to the tick method in the output handlers
     *
     * @param int    $status SUCCESS (default), SKIP, or FAIL
     * @param string $msg    Message to include for this report
     * @param array  $extraInfo
     * @param int    $incrementBy
     * @return Report
     */
    public function tick($status = Tick::SUCCESS, $msg = null, array $extraInfo = [], $incrementBy = 1)
    {
        if ( ! $this->isRunning()) {
            $this->start();
        }

        $tick = new Tick($this, $status, $msg, $extraInfo, $incrementBy);
        // Increment the counter
        if (array_key_exists($tick->getStatus(), $this->numProcessedItems)) {
            $this->numProcessedItems[$tick->getStatus()] += $tick->getIncrementBy();
        }
        else {
            $this->numProcessedItems[$tick->getStatus()] = $tick->getIncrementBy();
        }

        $this->dispatcher->dispatch(Events::TRACKER_TICK, $tick);

        $this->lastTick = $tick;
        return $tick->getReport();
    }

    /**
     * Finish processing
     *
     * Builds a report and sends it to the finish method in the output handlers
     *
     * @param string $msg Optional message to include
     * @param array  $extraInfo
     * @return Report
     */
    public function finish($msg = null, array $extraInfo = [])
    {
        if ( ! $this->isRunning()) {
            throw new TrackerException("Cannot finish Tracker.  Not running.");
        }

        $tick = new Tick($this, Tick::SUCCESS, $msg, $extraInfo, 0);
        $this->status = self::FINISHED;

        $this->dispatcher->dispatch(Events::TRACKER_FINISH, $tick);

        $this->lastTick = $tick;
        return $tick->getReport();
    }

    /**
     * Abort processing
     *
     * Builds a reports and send it to the abort method in the output handlers
     *
     * @param string $msg Optional message to include
     * @param array  $extraInfo
     * @return Report
     */
    public function abort($msg = null, array $extraInfo = [])
    {
        if ( ! $this->isRunning()) {
            throw new TrackerException("Cannot abort Tracker.  Not running.");
        }

        $tick = new Tick($this, Tick::FAIL, $msg, $extraInfo, 0);
        $this->status = self::ABORTED;

        $this->dispatcher->dispatch(Events::TRACKER_ABORT, $tick);
        
        $this->lastTick = $tick;
        return $tick->getReport();
    }
}
