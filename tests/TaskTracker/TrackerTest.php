<?php

namespace TaskTracker;

/**
 * Unit test suite for the Tracker class
 */
class TrackerTest extends \PHPUnit_Framework_TestCase
{
    private $lastOutputHanderContent = array();

    // --------------------------------------------------------------

    public function tearDown()
    {
        $this->lastOutputHanderContent = array();
        parent::tearDown();
    }

    // --------------------------------------------------------------

    public function testInstantiateSucceeds()
    {
        $obj = $this->getObj();
        $this->assertInstanceOf('\TaskTracker\Tracker', $obj);
    }

    // --------------------------------------------------------------

    public function testNonOutputHandlerClassProducesError()
    {
        $this->setExpectedException("PHPUnit_Framework_Error");
        $obj = new Tracker(new \stdClass);
    }

    // --------------------------------------------------------------

    /**
     * Tests the report generation method and the start() method
     */
    public function testSingleTick()
    {
        $obj = $this->getObj();
        $obj->tick('hay', 2);
        $result = $this->lastOutputHanderContent[0];

        $this->assertInstanceOf('\TaskTracker\Report', $result);
        $this->assertEquals(2, $result->numItems);
        $this->assertEquals('hay', $result->currMessage);
    }

    // --------------------------------------------------------------

    public function testMultipleTicks()
    {
        $obj = $this->getObj();
        $obj->tick('hay', 2);
        usleep(250000);
        $obj->tick('there', 1);
        usleep(500000);
        $obj->tick('pal', 2);

        $result = $this->lastOutputHanderContent[0];
        $this->assertGreaterThan(0.75, $result->timeTotal);
        $this->assertGreaterThan(0.5, $result->timeSinceLastTick);
        $this->assertEquals(3, $result->numTicks);
        $this->assertEquals(5, $result->numItems);
        $this->assertEquals('pal', $result->currMessage);
    }

    // --------------------------------------------------------------

    public function testAbort()
    {
        $obj = $this->getObj();
        $obj->tick('hey', 1);
        $obj->abort('Failed');

        $result = $this->lastOutputHanderContent[0];
        $this->assertEquals('abort', $result->action);
        $this->assertEquals(1, $result->numTicks);
    }

    // --------------------------------------------------------------

    public function testFinish()
    {
        $obj = $this->getObj();
        $obj->tick('hey', 1);
        $obj->finish('All Done');

        $result = $this->lastOutputHanderContent[0];
        $this->assertEquals('finish', $result->action);
        $this->assertEquals(1, $result->numTicks);
    }

    // --------------------------------------------------------------

    /**
     * Get an object
     *
     * @param int $numItems  -1 is infinite
     * @return Tracker
     */
    protected function getObj($numItems = -1)
    {
        //Get a mock outputHandler
        $handler = $this->getMock('TaskTracker\OutputHandler\OutputHandler');

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
        $this->lastOutputHanderContent = $args;
    }
}


/* EOF: TrackerTest.php */