<?php
/**
 * tasktracker
 *
 * @license ${LICENSE_LINK}
 * @link ${PROJECT_URL_LINK}
 * @version ${VERSION}
 * @package ${PACKAGE_NAME}
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * ------------------------------------------------------------------
 */

namespace TaskTracker;

/**
 * Defines Tracker Events
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
