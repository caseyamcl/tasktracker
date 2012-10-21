<?php

namespace TaskTracker;

/**
 * Unit test suite for the Tracker class
 */
class TrackerTest extends \PHPUnit_Framework_TestCase
{
    private $lastOutputHandlerContent = array();

    // --------------------------------------------------------------

    public function tearDown()
    {
        $this->lastOutputHandlerContent = array();
        parent::tearDown();
    }

    // --------------------------------------------------------------

    public function testInstantiateSucceeds()
    {
        $obj = $this->getObj();
        $this->assertInstanceOf('\TaskTracker\Tracker', $obj);
    }

    // --------------------------------------------------------------

    public function testUnknownConstantEqualsInfiniteConstantInReport()
    {
        $this->assertEquals(Report::INFINITE, Tracker::UNKNOWN);
    }

    // --------------------------------------------------------------

    public function testStart()
    {
        $obj = $this->getObj();
        $obj->start("Starting task");
        $this->assertInstanceOf('TaskTracker\Report', $this->lastOutputHandlerContent[0]);
        $this->assertEquals("Starting task", $this->lastOutputHandlerContent[1]);
    }

    // --------------------------------------------------------------

    public function testTicks()
    {
        $obj = $this->getObj();
        $obj->start();
        $obj->tick('One');
        $obj->tick('Two', Tick::SKIP);

        $this->assertInstanceOf('TaskTracker\Report', $this->lastOutputHandlerContent[0]);
        $this->assertEquals("Two", $this->lastOutputHandlerContent[1]);
        $this->assertEquals(2, $this->lastOutputHandlerContent[0]->numItems);
    }

    // --------------------------------------------------------------

    public function testTicksWithNoExplicitCallToStart()
    {
        $obj = $this->getObj();
        $obj->start();
        $obj->tick('One');
        $obj->tick('Two', Tick::SKIP);

        $this->assertInstanceOf('TaskTracker\Report', $this->lastOutputHandlerContent[0]);
        $this->assertEquals("Two", $this->lastOutputHandlerContent[1]);
        $this->assertEquals(2, $this->lastOutputHandlerContent[0]->numItems);
    }

    // --------------------------------------------------------------

    public function testFinish()
    {
        $obj = $this->getObj();
        $obj->start();
        $obj->tick('One');
        $obj->finish('All Done');

        $this->assertInstanceOf('TaskTracker\Report', $this->lastOutputHandlerContent[0]);
        $this->assertEquals("All Done", $this->lastOutputHandlerContent[1]);
        $this->assertEquals(1, $this->lastOutputHandlerContent[0]->numItems);
    }

    // --------------------------------------------------------------

    public function testAbort()
    {
        $obj = $this->getObj();
        $obj->start();
        $obj->abort('Oops');

        $this->assertInstanceOf('TaskTracker\Report', $this->lastOutputHandlerContent[0]);
        $this->assertEquals("Oops", $this->lastOutputHandlerContent[1]);
        $this->assertEquals(0, $this->lastOutputHandlerContent[0]->numItems);
    }

    // --------------------------------------------------------------

    public function testFinishThrowsExceptionForNonStartedTask()
    {
        $this->setExpectedException("\RuntimeException");

        $obj = $this->getObj();
        $obj->finish("Done");
    }

    // --------------------------------------------------------------

    public function testAbortThrowsExceptionForNonStartedTask()
    {
        $this->setExpectedException("\RuntimeException");

        $obj = $this->getObj();
        $obj->abort("Nuts");
    }

    // --------------------------------------------------------------

    public function testFiniteTrackerReportProducesExpectedValues()
    {
        $obj = $this->getObj(20);
        $obj->start();
        $obj->tick('One');
        $obj->tick('Two', Tick::SKIP);

        $this->assertInstanceOf('TaskTracker\Report', $this->lastOutputHandlerContent[0]);
        $this->assertEquals(20, $this->lastOutputHandlerContent[0]->totalItems);
    }

    // --------------------------------------------------------------

    /**
     * Get an object
     *
     * @param int $numItems  -1 is infinite
     * @return Tracker
     */
    protected function getObj($numItems = Tracker::UNKNOWN)
    {
        //Get a mock outputHandler
        $handler = $this->getMock('TaskTracker\OutputHandler\OutputHandler');

        $handler
            ->expects($this->any())->method('start')
            ->will($this->returnCallback(array($this, 'outputHandlerCallback')));
        $handler
            ->expects($this->any())->method('tick')
            ->will($this->returnCallback(array($this, 'outputHandlerCallback')));
        $handler
            ->expects($this->any())->method('finish')
            ->will($this->returnCallback(array($this, 'outputHandlerCallback')));
        $handler
            ->expects($this->any())->method('abort')
            ->will($this->returnCallback(array($this, 'outputHandlerCallback')));

        return new Tracker($handler, $numItems);
    }

    // --------------------------------------------------------------

    /**
     * Callback for the task tracker mock object
     */
    public function outputHandlerCallback()
    {
        $args = func_get_args();
        $this->lastOutputHandlerContent = $args;
    }
}


/* EOF: TrackerTest.php */