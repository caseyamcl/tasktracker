<?php

namespace TaskTracker\OutputHandler;
use TaskTracker\TestFixtures\FakeReportFinite;
use TaskTracker\TestFixtures\FakeReportInfinite;

/**
 * Unit test suite for the Symfony Console OutputHandler test
 */
class MonologTest extends \PHPUnit_Framework_TestCase
{
    private $loggerOutput = array();

    // --------------------------------------------------------------

    public function tearDown()
    {
        $this->loggerOutput = array();
        parent::tearDown();
    }

    // --------------------------------------------------------------

    public function testInstantiateAsObjectSucceeds()
    {
        $obj = $this->getObj();
        $this->assertInstanceOf('\TaskTracker\OutputHandler\Monolog', $obj);
    }

    // --------------------------------------------------------------

    public function testTickSucceeds()
    {
        $obj = $this->getObj();
        $report = $this->getFakeReport();

        $obj->tick($report);
        $result = $this->loggerOutput;
        
        $this->assertEquals('Test Message', $result[0][0]);
        $this->assertEquals((array) $report, $result[0][1]);
    }


    // --------------------------------------------------------------

    public function testAbortSucceeds()
    {
        $obj = $this->getObj();
        $report = $this->getFakeReport();

        $obj->abort($report);
        $result = $this->loggerOutput[0];
        
        $this->assertEquals('Aborting. . . Test Message', $result[0]);
        $this->assertEquals((array) $report, $result[1]);        
    }

    // --------------------------------------------------------------

    public function testFinishSucceeds()
    {
        $obj = $this->getObj();
        $report = $this->getFakeReport(true);

        $obj->finish($report);
        $result = $this->loggerOutput[0];
        
        $this->assertEquals('Finishing. . . Test Message', $result[0]);
        $this->assertEquals((array) $report, $result[1]);                
    }

    // --------------------------------------------------------------

    public function testTickHonorsInterval()
    {
        $obj = $this->getObj();
        $obj->setLogInterval(1);

        $report = $this->getFakeReport();

        $obj->tick($report);
        usleep(500000);
        $obj->tick($report);
        usleep(600000);
        $obj->tick($report);

        $this->assertEquals(2, count($this->loggerOutput));
    }

    // --------------------------------------------------------------

    protected function getFakeReport($finite = false)
    {
        include_once(__DIR__ . '/../../fixtures/FakeReportFinite.php');
        include_once(__DIR__ . '/../../fixtures/FakeReportInfinite.php');

        return ($finite) ? new FakeReportFinite : new FakeReportInfinite;
    }

    // --------------------------------------------------------------

    /**
     * Get the object with appropriate mocks
     *
     * @return TaskTracker\OutputHandler\SymfonyConsole
     */
    protected function getObj()
    {
        $mockLogger = $this->getMock('\Monolog\Logger', array('addInfo', 'addError'), array(), '', false);

        $mockLogger
            ->expects($this->any())
            ->method('addInfo')
            ->will($this->returnCallback(array($this, 'mockLoggerCallback')));

        $mockLogger
            ->expects($this->any())
            ->method('addError')
            ->will($this->returnCallback(array($this, 'mockLoggerCallback')));

        return new Monolog($mockLogger);
    }

    // --------------------------------------------------------------

    /**
     * Callback for mock object just regurgitates the input
     *
     * @param string $input
     * @return string
     */
    public function mockLoggerCallback()
    {
        $this->loggerOutput[] = func_get_args();
    }
}
/* EOF: MonologTest.php */