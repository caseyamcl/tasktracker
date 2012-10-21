<?php

namespace TaskTracker;

class ReportTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiateSucceeds()
    {
        $obj = new Report();
        $this->assertInstanceOf('\TaskTracker\Report', $obj);
    }

    // --------------------------------------------------------------

    public function testInitialValuesAreCorrect()
    {
        $startTime = microtime(true);

        $obj = new Report(Report::INFINITE, $startTime);
        $this->assertEquals($startTime, $obj->startTime);
        $this->assertEquals(Report::INFINITE, $obj->totalItems);
        $this->assertEquals(0, $obj->numItems);
        $this->assertEquals(0, $obj->timeElapsed);
        $this->assertGreaterThan(0, $obj->peakMemory);
        $this->assertEquals(0, $obj->numItemsSuccess);
        $this->assertEquals(0, $obj->numItemsFail);
        $this->assertEquals(0, $obj->numItemsSkip);
        $this->assertEquals(0, $obj->itemTime);
        $this->assertEquals(0, $obj->maxItemTime);
        $this->assertEquals(0, $obj->minItemTime);
        $this->assertEquals(0, $obj->avgItemTime);
        $this->assertTrue( ! isset($obj->message));
        $this->assertTrue( ! isset($obj->memUsage));
        $this->assertTrue( ! isset($obj->timestamp));
        $this->assertTrue( ! isset($obj->status));
    }

    // --------------------------------------------------------------

    public function testValuesAfterOneTickAreCorrect()
    {
        //Start a new report
        $startTime = microtime(true);
        $obj = new Report(20, $startTime);

        //Sleep and do a tick
        usleep(0250000);
        $obj->tick(new Tick('Test Tick'));

        $this->assertEquals($startTime, $obj->startTime);
        $this->assertEquals(20, $obj->totalItems);
        $this->assertEquals(1, $obj->numItems);
        $this->assertGreaterThan(0, $obj->timeElapsed);
        $this->assertGreaterThan(0, $obj->peakMemory);
        $this->assertEquals(1, $obj->numItemsSuccess);
        $this->assertEquals(0, $obj->numItemsFail);
        $this->assertEquals(0, $obj->numItemsSkip);
        $this->assertEquals($obj->timeElapsed, $obj->itemTime);
        $this->assertEquals($obj->itemTime, $obj->maxItemTime);
        $this->assertEquals($obj->itemTime, $obj->minItemTime);
        $this->assertEquals($obj->itemTime, $obj->avgItemTime);
        $this->assertEquals('Test Tick', $obj->message);
        $this->assertGreaterThan(0, $obj->memUsage);
        $this->assertEquals($obj->startTime + $obj->timeElapsed, $obj->timestamp);
        $this->assertEquals(Tick::SUCCESS, $obj->status);
    }

    // --------------------------------------------------------------

    public function testValuesAfterMultipleTicksAreCorrect()
    {
        //Start a new report
        $startTime = microtime(true);
        $obj = new Report(Report::INFINITE, $startTime);

        //Sleep and do ticks
        usleep(0500000);
        $obj->tick(new Tick('Test Tick'));
        usleep(0500000);
        $obj->tick(new Tick('Test Tick 1', Tick::FAIL));
        usleep(1000000);
        $obj->tick(new Tick('Test Tick 2', Tick::SKIP));

        $this->assertEquals($startTime, $obj->startTime);
        $this->assertEquals(Report::INFINITE, $obj->totalItems);
        $this->assertEquals(3, $obj->numItems);
        $this->assertGreaterThan(0, $obj->timeElapsed);
        $this->assertGreaterThan(0, $obj->peakMemory);
        $this->assertEquals(1, $obj->numItemsSuccess);
        $this->assertEquals(1, $obj->numItemsFail);
        $this->assertEquals(1, $obj->numItemsSkip);
        $this->assertGreaterThan(1.0, $obj->itemTime);
        $this->assertEquals($obj->itemTime, $obj->maxItemTime);
        $this->assertLessThan(0.6, $obj->minItemTime);
        $this->assertTrue($obj->avgItemTime > $obj->minItemTime && $obj->avgItemTime < $obj->maxItemTime);
        $this->assertEquals('Test Tick 2', $obj->message);
        $this->assertGreaterThan(0, $obj->memUsage);
        $this->assertGreaterThan($startTime, $obj->timestamp);
        $this->assertEquals(Tick::SKIP, $obj->status);

    }
}

/* EOF: ReportTest.php */