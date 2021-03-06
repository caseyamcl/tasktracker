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

use TaskTracker\Test\Fixture\ArrayRecordingSubscriber;
use TaskTracker\Tick;
use TaskTracker\Tracker;

/**
 * Class TrackerTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class TrackerTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiateSucceeds()
    {
        $obj= $this->getTrackerObj();
        $this->assertInstanceOf('\TaskTracker\Tracker', $obj);
    }

    // ---------------------------------------------------------------

    public function testGetDispatcherReturnsEventDispatcher()
    {
        $obj = $this->getTrackerObj();
        $this->assertInstanceOf(
            '\Symfony\Component\EventDispatcher\EventDispatcherInterface',
            $obj->getDispatcher()
        );
    }

    // ---------------------------------------------------------------

    public function testGetNumTotalItemsReturnsExpectedValue()
    {
        $obj = $this->getTrackerObj();
        $this->assertEquals(-1, $obj->getNumTotalItems());

        $obj = $this->getTrackerObj(8);
        $this->assertEquals(8, $obj->getNumTotalItems());
    }

    // ---------------------------------------------------------------

    public function testGetProcessedItemsReturnsZeroIfNotStarted()
    {
        $obj = $this->getTrackerObj(8);
        $this->assertEquals(0, $obj->getNumProcessedItems());
    }

    // ---------------------------------------------------------------

    public function testGetProcessedItemsReturnsExpectedValuesWhileRunning()
    {
        $obj = $this->getTrackerObj(12);

        $obj->start();
        for ($i = 1; $i <= 12; $i++) {
            if ($i <= 3) {
                $obj->tick(Tick::SUCCESS);
            }
            elseif ($i <= 6) {
                $obj->tick(Tick::SKIP);
            }
            else {
                $obj->tick(Tick::FAIL);
            }
        }
        $obj->finish();

        $this->assertEquals(12, $obj->getNumProcessedItems());
        $this->assertEquals(3, $obj->getNumProcessedItems(Tick::SUCCESS));
        $this->assertEquals(3, $obj->getNumProcessedItems(Tick::SKIP));
        $this->assertEquals(6, $obj->getNumProcessedItems(Tick::FAIL));
    }

    // ---------------------------------------------------------------

    public function testIsRunningReturnsFalseIfNotStarted()
    {
        $obj = $this->getTrackerObj();
        $this->assertFalse($obj->isRunning());
    }

    // ---------------------------------------------------------------

    public function testIsRunningReturnsTrueIfStarted()
    {
        $obj = $this->getTrackerObj();
        $obj->start();
        $this->assertTrue($obj->isRunning());
    }

    // ---------------------------------------------------------------

    public function testStartFailsOnSecondCall()
    {
        $this->setExpectedException('TaskTracker\TrackerException');

        $obj = $this->getTrackerObj();
        $obj->start();
        $obj->start();
    }

    // ---------------------------------------------------------------

    public function testTickAutoStarts()
    {
        $obj = $this->getTrackerObj();
        $obj->tick();

        $this->assertTrue($obj->isRunning());
    }

    // ---------------------------------------------------------------

    public function testFinishThrowsExceptionIfNotStarted()
    {
        $this->setExpectedException('TaskTracker\TrackerException');

        $obj = $this->getTrackerObj();
        $obj->finish();
    }

    // ---------------------------------------------------------------

    public function testAbortThrowsExceptionIfNotStarted()
    {
        $this->setExpectedException('TaskTracker\TrackerException');

        $obj = $this->getTrackerObj();
        $obj->abort();
    }

    // ---------------------------------------------------------------

    public function testTickReturnsReport()
    {
        $obj = $this->getTrackerObj();
        $report = $obj->tick();

        $this->assertInstanceOf('\TaskTracker\Report', $report);
    }

    // ---------------------------------------------------------------

    public function testFinishReturnsReport()
    {
        $obj = $this->getTrackerObj();
        $obj->tick();
        $obj->tick();
        $obj->tick();
        $report = $obj->finish();

        $this->assertInstanceOf('\TaskTracker\Report', $report);
    }

    // ---------------------------------------------------------------

    public function testAbortReturnsReport()
    {
        $obj = $this->getTrackerObj();
        $obj->tick();
        $obj->tick();
        $obj->tick();

        $report = $obj->abort();

        $this->assertInstanceOf('\TaskTracker\Report', $report);
    }

    // ---------------------------------------------------------------

    public function testEventDispatch()
    {
        $sub = new ArrayRecordingSubscriber();

        $tracker = $this->getTrackerObj();
        $tracker->addSubscriber($sub);
        $tracker->tick(Tick::SUCCESS, 'msg1');
        $tracker->tick(Tick::SUCCESS, 'msg2');

        $this->assertEquals(2, count($sub->getItems()));
        $this->assertEquals(['msg1', 'msg2'], $sub->getItems());
    }

    // ---------------------------------------------------------------

    public function testBuildConstructorBuildsWithSelectedSubscribers()
    {
        $sub = new ArrayRecordingSubscriber();
        $tracker = Tracker::build([$sub], 2);

        $tracker->tick(Tick::SUCCESS, 'msg1');
        $tracker->tick(Tick::SUCCESS, 'msg2');

        $this->assertEquals(2, count($sub->getItems()));
        $this->assertEquals(['msg1', 'msg2'], $sub->getItems());
    }

    // ---------------------------------------------------------------

    public function testRunMethod()
    {
        $tracker = Tracker::build([], 10);
        $iterator = new \ArrayIterator(['msg1', 'msg2']);

        $lastReport = $tracker->run($iterator, function(Tracker $tracker, $item) {
            $tracker->tick(Tick::SUCCESS, $item);
        });

        $this->assertEquals(2, $lastReport->getNumItemsProcessed());
    }

    // ---------------------------------------------------------------

    protected function getTrackerObj($num = Tracker::UNKNOWN)
    {
        return new Tracker($num);
    }
}
