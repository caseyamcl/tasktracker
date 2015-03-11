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

namespace TaskTracker\Test\Fixture;

use TaskTracker\Tick;

/**
 * Builds test ticks for testing
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
trait TickBuilderTrait
{
    protected function getTick($status = Tick::SUCCESS, $message = '', array $extra = [])
    {
        $tracker = \Mockery::mock('\TaskTracker\Tracker');
        $tracker->shouldReceive('getStartTime')->andReturn(100);
        $tracker->shouldReceive('getNumTotalItems')->andReturn(25);
        $tracker->shouldReceive('getLastTick')->andReturn(null);
        $tracker->shouldReceive('getNumProcessedItems')->andReturn(3);

        return new Tick($tracker, $status, $message, $extra);
    }
}
