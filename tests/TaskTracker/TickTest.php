<?php

namespace TaskTracker;

/**
 * Unit test suite for the Tick class
 */
class TickTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiateSucceeds()
    {
        $obj = new Tick();
        $this->assertInstanceOf('\TaskTracker\Tick', $obj);
    }

    // --------------------------------------------------------------

    public function testInvalidTickStatusThrowsException()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $obj = new Tick(null, -2);
    }

    // --------------------------------------------------------------

    /**
     * @dataProvider dataProvider
     */
    public function testTickProducesExpectedParameters($msg, $status)
    {
        $obj = new Tick($msg, $status);
        $this->assertEquals($msg, $obj->message);
        $this->assertEquals($status, $obj->status);
    }    

    // --------------------------------------------------------------

    /**
     * @dataProvider dataProvider
     */
    public function testToArrayProducesExpectedArrays($msg, $status)
    {
        $obj = new Tick($msg, $status);
        $arr = $obj->toArray();
        $expected = array('message' => $msg, 'status' => $status);

        $this->assertEquals(4, count($arr));
        $this->assertEquals($expected['message'], $arr['message']);
        $this->assertEquals($expected['status'], $arr['status']);
    }

    // --------------------------------------------------------------

    /**
     * Data provider for testTickProducesExpectedParameters()
     */
    public function dataProvider()
    {
        return array(
            array("Hi", Tick::SUCCESS),
            array(null, Tick::FAIL),
            array(1234, Tick::SKIP)
        );
    }    
}

/* EOF: TickTest.php */