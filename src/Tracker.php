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

    private EventDispatcherInterface $dispatcher;

    private int $numTotalItems;

    private array $numProcessedItems;

    private ?float $startTime;

    private ?Tick $lastTick = null;

    private int $status;

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
    public static function build(array $subscribers = [], int $totalItems = self::UNKNOWN): self
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
     * @param int $totalItems Default is unknown (-1)
     * @param EventDispatcherInterface|null $dispatcher
     */
    public function __construct(
        int $totalItems = self::UNKNOWN,
        ?EventDispatcherInterface $dispatcher = null
    ) {
        $this->status            = self::NOT_STARTED;
        $this->dispatcher        = $dispatcher ?: new EventDispatcher();
        $this->numTotalItems     = $totalItems;
        $this->numProcessedItems = [];
    }

    /**
     * Iterate over items and track them
     *
     * @param iterable $items
     * @param callable $itemCallback  Callback accepts arguments: (Tracker $tracker, $item)
     * @return Report  The final report
     */
    public function run(iterable $items, callable $itemCallback): Report
    {
        $this->start();
        foreach ($items as $item) {
            call_user_func($itemCallback, $this, $item);
        }
        return $this->finish();
    }

    public function getDispatcher(): EventDispatcherInterface
    {
        return $this->dispatcher;
    }

    /**
     * Add a subscriber to this Tracker instance
     */
    public function addSubscriber(EventSubscriberInterface $subscriber): void
    {
        $this->dispatcher->addSubscriber($subscriber);
    }

    /**
     * Get the number of total items to be processed (-1 for unknown)
     */
    public function getNumTotalItems(): int
    {
        return $this->numTotalItems;
    }

    /**
     * Return the number of items processed, including failed/succeeded
     *
     * If $tickType is specified, only return the item count of that type
     * Tick::SUCCESS, Tick::FAIL, Tick::SKIP, or null for all
     */
    public function getNumProcessedItems(?int $tickType = null): int
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
     */
    public function getStartTime(): ?float
    {
        return $this->startTime;
    }

    /**
     * Get the last report
     *
     * Returns NULL if not started
     */
    public function getLastTick(): ?Tick
    {
        return $this->lastTick;
    }

    /**
     * Get the status
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Is the tracker running?
     */
    public function isRunning(): bool
    {
        return ($this->getStatus() == self::RUNNING);
    }

    /**
     * Start processing
     *
     * If this method is not called explicitly, it will automatically
     * be called upon the first tick
     *
     * @param string|null $msg       Optional message to include
     * @param array       $extraInfo Optional extra info to include
     * @return void
     * @throws TrackerException  If called when already started
     */
    public function start(?string $msg = null, array $extraInfo = []): void
    {
        if ($this->status != self::NOT_STARTED) {
            throw new TrackerException("Cannot start tracker that was already started");
        }

        $this->status = self::RUNNING;
        $this->startTime = microtime(true);

        $tick = new Tick($this, Tick::SUCCESS, $msg, $extraInfo, 0);
        $this->dispatcher->dispatch($tick, Events::TRACKER_START->value);
        $this->lastTick = $tick;
    }

    /**
     * Indicate progress to the tracker
     *
     * Builds a report and send it to the tick method in the output handlers
     *
     * @param int         $status SUCCESS (default), SKIP, or FAIL
     * @param string|null $msg    Message to include in this report
     * @param array       $extraInfo
     * @param int         $incrementBy
     * @return Report
     */
    public function tick(
        int $status = Tick::SUCCESS,
        ?string $msg = null,
        array $extraInfo = [],
        int $incrementBy = 1
    ): Report {
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

        $this->dispatcher->dispatch($tick, Events::TRACKER_TICK->value);

        $this->lastTick = $tick;
        return $tick->getReport();
    }

    /**
     * Finish processing
     *
     * Builds a report and sends it to the finish method in the output handlers
     *
     * @param string|null $msg  Optional message to include
     * @param array  $extraInfo Optional extra info to include
     * @return Report
     */
    public function finish(?string $msg = null, array $extraInfo = []): Report
    {
        if ( ! $this->isRunning()) {
            throw new TrackerException("Cannot finish Tracker.  Not running.");
        }

        $tick = new Tick($this, Tick::SUCCESS, $msg, $extraInfo, 0);
        $this->status = self::FINISHED;

        $this->dispatcher->dispatch($tick, Events::TRACKER_FINISH->value);

        $this->lastTick = $tick;
        return $tick->getReport();
    }

    /**
     * Abort processing
     *
     * Builds a report and sends it to the abort method in the output handlers
     *
     * @param string|null $msg Optional message to include
     * @param array $extraInfo Optional extra info to include
     * @return Report
     */
    public function abort(?string $msg = null, array $extraInfo = []): Report
    {
        if ( ! $this->isRunning()) {
            throw new TrackerException("Cannot abort Tracker.  Not running.");
        }

        $tick = new Tick($this, Tick::FAIL, $msg, $extraInfo, 0);
        $this->status = self::ABORTED;

        $this->dispatcher->dispatch($tick, Events::TRACKER_ABORT->value);
        
        $this->lastTick = $tick;
        return $tick->getReport();
    }
}
