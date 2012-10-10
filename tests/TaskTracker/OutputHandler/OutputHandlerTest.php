<?php

namespace TaskTracker\OutputHandler;

/**
 * Unit test suite for the abstract OutputHandler test
 */
class OutputHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerFormatTimeMethod
     */    
    public function testFormatTimeReturnsValidResults($seconds, $expectedString)
    {
        $obj = $this->getObj();
        $this->assertEquals($expectedString, $obj->formatTime($seconds));
    }

    // --------------------------------------------------------------

    /**
     * Data provider for testFormatTimeReturnsValidResults
     */
    public function providerFormatTimeMethod()
    {
        return array(
            array(30,       '00:30'),
            array(60,       '01:00'),
            array(0.1,      '00:00'),
            array(3800,     '1:03:20'),
            array(86401,    '24:00:01'),
            array(86501.25, '24:01:41')
        );
    }

    // --------------------------------------------------------------

    protected function getObj()
    {
        return $this->getMockForAbstractClass('\TaskTracker\OutputHandler\OutputHandler');
    }
}

/* EOF: OutputterTest.php */