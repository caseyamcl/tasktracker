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

use InvalidArgumentException;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Task Tracker Tick
 *
 * Represents a progress tick
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class Tick extends GenericEvent implements TickInterface
{
    const SUCCESS  = 1;
    const FAIL     = 0;
    const SKIP     = -1;

    /**
     * @var string  Custom Message
     */
    private string $message;

    /**
     * @var float  Timestamp (microtime float)
     */
    private float $timestamp;

    /**
     * @var int  The status of this tick (success, fail, or skip)
     */
    private int $status;

    /**
     * @var int
     */
    private int $incrementBy;

    /**
     * @var Report
     */
    private Report $report;

    /**
     * @var array
     */
    private array $extraInfo = [];

    /**
     * Constructor
     *
     * @param Tracker      $tracker
     * @param int          $status
     * @param string|null  $message
     * @param array        $extraInfo
     * @param int         $incrementBy
     */
    public function __construct(
        Tracker $tracker,
        int $status = self::SUCCESS,
        ?string $message = '',
        array $extraInfo = [],
        int $incrementBy = 1
    )  {
        parent::__construct();
        if ( ! in_array($status, [self::FAIL, self::SKIP, self::SUCCESS])) {
            throw new InvalidArgumentException("Invalid tick status");
        }

        //Set parameters
        $this->incrementBy = $incrementBy;
        $this->status      = $status;
        $this->timestamp   = microtime(true);
        $this->message     = $message ?: '';
        $this->extraInfo   = $extraInfo;
        $this->report      = new Report($this, $tracker);
    }

    /**
     * Returns the message associated with the Tick event
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Returns the timestamp (microtime float) of the Tick event
     */
    public function getTimestamp(): float
    {
        return $this->timestamp;
    }

    /**
     * Returns the status (Tick::SUCCESS, Tick::FAIL, TICK::SKIP) of the Tick event
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Returns the number of increments associated with the Tick event

     */
    public function getIncrementBy(): int
    {
        return $this->incrementBy;
    }

    /**
     * Returns the report associated with the Tick event
     */
    public function getReport(): Report
    {
        return $this->report;
    }

    /**
     * Returns any extra information associated with the Tick event
     */
    public function getExtraInfo(): array
    {
        return $this->extraInfo;
    }
} 
