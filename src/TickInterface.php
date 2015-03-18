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
     *
     * @return string
     */
    public function getMessage();

    /**
     * Returns the timestamp (microtime float) of the Tick event
     *
     * @return float
     */
    public function getTimestamp();

    /**
     * Returns the status (Tick::SUCCESS, Tick::FAIL, TICK::SKIP) of the Tick event
     *
     * @return int
     */
    public function getStatus();

    /**
     * Returns the number of increments associated with with the Tick event
     *
     * @return int
     */
    public function getIncrementBy();


    /**
     * Returns the report associated with the Tick event
     *
     * @return Report
     */
    public function getReport();

    /**
     * Returns any extra information associated with the Tick event
     *
     * @return array
     */
    public function getExtraInfo();
}
