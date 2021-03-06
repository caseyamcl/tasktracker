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
 * Tracker Exceptions are thrown only when the Tracker class is misused
 *
 * For example, this exception is thrown when someone attempts to call $tracker->finish()
 * without having called $tracker->start() or $tracker->tick()
 *
 * No runtime exceptions from the actual task being worked on are handled
 * by this library.
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class TrackerException extends \RuntimeException
{
    // pass
}
