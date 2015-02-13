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

namespace TaskTracker\Test;

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
        $obj = $this->getTrackerObj();

    }

    // ---------------------------------------------------------------

    protected function getTrackerObj($num = Tracker::UNKNOWN)
    {
        return new Tracker($num);
    }
}