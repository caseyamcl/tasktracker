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
 * Task Tracker Events
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
final class Events
{
    /**
     * Tracker Start Event
     */
    const TRACKER_START = 'tracker.start';

    /**
     * Tracker Tick Event
     */
    const TRACKER_TICK  = 'tracker.tick';

    /**
     * Tracker Finish Event
     */
    const TRACKER_FINISH = 'tracker.finsh';

    /**
     * Tracker Abort Event
     */
    const TRACKER_ABORT = 'tracker.abort';
}
