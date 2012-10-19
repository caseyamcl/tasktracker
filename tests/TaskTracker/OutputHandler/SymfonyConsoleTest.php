<?php

namespace TaskTracker\OutputHandler;
use TaskTracker\TestFixtures\FakeReportFinite;
use TaskTracker\TestFixtures\FakeReportInfinite;

/**
 * Unit test suite for the Symfony Console OutputHandler test
 */
class SymfonyConsoleTest extends \PHPUnit_Framework_TestCase
{
    private $writelnOutput = array();

    // --------------------------------------------------------------

    public function tearDown()
    {
        $this->writelnOutput = array();
        parent::tearDown();
    }

    // --------------------------------------------------------------

    public function testInstantiateSucceeds()
    {
        $obj = $this->getObj();
        $this->assertInstanceOf('\TaskTracker\OutputHandler\SymfonyConsole', $obj);
    }    

    // --------------------------------------------------------------

    public function testTickReturnsCorrectStringForInfniteTask()
    {
        $obj = $this->getObj();
        $report = $this->getFakeReport();

        //Do the test
        $obj->tick($report);

        //Check output
        $expected = array(
            "\033[F\033[F\033[F",
            'Test Message ..',
            '25 | 1:00:12 | Mem: 3.00mb (max: 4.04mb) | Avg: 2.15s Max: 3.04s Min: 0.30s'
            . "\n" . '15 processed | 3 skipped | 3 failed'
        );

        $this->assertEquals($this->writelnOutput, $expected);
    }

    // --------------------------------------------------------------

    public function testTickReturnsCorrectStringForFiniteTask()
    {
        $obj = $this->getObj();
        $report = $this->getFakeReport(true);

        //Do the test
        $obj->tick($report);

        //Check line one (since the console width is variable, we check using regex)
        $this->assertRegexp("/^Test Message \[83\%[=]+>([ ]+)?\](\s+)?$/", $this->writelnOutput[1]);

        $expectedLineTwo = "25 | 1:00:12 | Mem: 3.00mb (max: 4.04mb) | Avg: 2.15s Max: 3.04s Min: 0.30s\n"
                            . "15 processed | 3 skipped | 3 failed";
                            
        $this->assertEquals($expectedLineTwo, $this->writelnOutput[2]);
    }

    // --------------------------------------------------------------

    public function testAbortReturnsExcpectedResult()
    {
        $obj = $this->getObj();
        $report = $this->getFakeReport(true);

        //Do the test
        $obj->abort($report);

        $this->assertEquals("\n\nAborting. . . Test Message\n\n", $this->writelnOutput[0]);
    }

    // --------------------------------------------------------------

    public function testFinishReturnsExpectedResult()
    {
        $obj = $this->getObj();
        $report = $this->getFakeReport(true);

        //Do the test     
        $obj->finish($report);

        $this->assertEquals("\n\nAll Done! Test Message\n\n", $this->writelnOutput[0]);    
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
        $mockSymConsoleUnit = $this->getMock(
            '\Symfony\Component\Console\Output\OutputInterface'
        );

        $mockSymConsoleUnit
            ->expects($this->any())
            ->method('write')
            ->will($this->returnCallback(array($this, 'mockSymConsoleUnitCallback')));

        $mockSymConsoleUnit
            ->expects($this->any())
            ->method('writeln')
            ->will($this->returnCallback(array($this, 'mockSymConsoleUnitCallback')));

        return new SymfonyConsole($mockSymConsoleUnit);
    }

    // --------------------------------------------------------------

    /**
     * Callback for mock object just regurgitates the input
     *
     * @param string $input
     * @return string
     */
    public function mockSymConsoleUnitCallback($input)
    {
        $this->writelnOutput[] = $input;
    }
}

/* EOF: SymfonyConsoleTest.php */