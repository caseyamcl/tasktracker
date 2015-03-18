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
 * Task Tracker Report Interface
 *
 * @package TaskTracker
 */
interface ReportInterface extends TickInterface
{
    /**
     * @return float
     */
    function getTimeStarted();

    /**
     * @return int
     */
    function getTotalItemCount();

    /**
     * @return Tick
     */
    function getTick();

    /**
     * @return int
     */
    function getNumItemsProcessed();

    /**
     * @return float
     */
    function getTimeElapsed();

    /**
     * @return int
     */
    function getNumItemsSuccess();

    /**
     * @return int
     */
    function getNumItemsFail();

    /**
     * @return int
     */
    function getNumItemsSkip();

    /**
     * @return float
     */
    function getItemTime();

    /**
     * @return float
     */
    function getMaxItemTime();

    /**
     * @return float
     */
    function getMinItemTime();

    /**
     * @return float
     */
    function getAvgItemTime();

    /**
     * @return int
     */
    function getMemUsage();

    /**
     * @return int
     */
    function getMemPeakUsage();
}
