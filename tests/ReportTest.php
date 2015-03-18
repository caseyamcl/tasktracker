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

namespace TaskTracker\Test;

use TaskTracker\Report;
use TaskTracker\Tick;

/**
 * Class ReportTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class ReportTest extends \PHPUnit_Framework_TestCase
{
    public function testGetReportObjSucceeds()
    {
        $obj = $this->getReportObj();
        $this->assertInstanceOf('\TaskTracker\Report', $obj);
    }

    // ---------------------------------------------------------------

    public function testToArrayReturnsExpectedValues()
    {
        $obj = $this->getReportObj();
        $this->assertEquals(
            array_keys($this->getFixtureData()),
            array_keys($obj->toArray())
        );
    }

    // ---------------------------------------------------------------

    /**
     * @return Report
     */
    protected function getReportObj()
    {
        $tick = \Mockery::mock('\TaskTracker\Tick');
        $tick->shouldReceive('getMessage')->andReturn('msg');
        $tick->shouldReceive('getTimestamp')->andReturn(234.45);
        $tick->shouldReceive('getStatus')->andReturn(Tick::SUCCESS);
        $tick->shouldReceive('getIncrementBy')->andReturn(1);
        $tick->shouldReceive('getExtraInfo')->andReturn(['foo' => 'bar']);

        $tracker = \Mockery::mock('\TaskTracker\Tracker');
        $tracker->shouldReceive('getStartTime')->andReturn(100);
        $tracker->shouldReceive('getNumTotalItems')->andReturn(25);
        $tracker->shouldReceive('getLastTick')->andReturn(null);
        $tracker->shouldReceive('getNumProcessedItems')->andReturn(3);

        return new Report($tick, $tracker);
    }

    // ---------------------------------------------------------------

    /**
     * @return array
     */
    protected function getFixtureData()
    {
        $file = __DIR__ . '/Fixture/reportFixture.json';
        return json_decode(file_get_contents($file), true);
    }
}
