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
