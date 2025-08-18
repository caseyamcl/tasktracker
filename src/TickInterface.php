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

/**
 * Tick Interface
 *
 * @package TaskTracker
 */
interface TickInterface
{
    /**
     * Returns the message associated with the Tick event
     */
    public function getMessage(): string;

    /**
     * Returns the timestamp (microtime float) of the Tick event
     */
    public function getTimestamp(): float;

    /**
     * Returns the status (Tick::SUCCESS, Tick::FAIL, TICK::SKIP) of the Tick event
     */
    public function getStatus(): int;

    /**
     * Returns the number of increments associated with with the Tick event
     */
    public function getIncrementBy(): int;


    /**
     * Returns the report associated with the Tick event
     */
    public function getReport(): Report;

    /**
     * Returns any extra information associated with the Tick event
     */
    public function getExtraInfo(): array;
}
