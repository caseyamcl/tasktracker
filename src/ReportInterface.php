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
    function getTimeStarted(): float;

    function getTotalItemCount(): int;

    function getTick(): Tick;

    function getNumItemsProcessed(): int;

    function getTimeElapsed(): float;

    function getNumItemsSuccess(): int;

    function getNumItemsFail(): int;

    function getNumItemsSkip(): int;

    function getItemTime(): float;

    function getMaxItemTime(): float;

    function getMinItemTime(): float;

    function getAvgItemTime(): float;

    function getMemUsage(): int;

    function getMemPeakUsage(): int;
}
