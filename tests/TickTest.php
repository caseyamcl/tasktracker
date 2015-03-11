<?php
use TaskTracker\Tick;

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

class TickTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiateTickSucceeds()
    {
        $obj =$this->getObject();
        $this->assertInstanceOf('\TaskTracker\Tick', $obj);
    }

    // ---------------------------------------------------------------

    public function testInstantiateWithInvalidStatusThrowsException()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $obj = $this->getObject(3);
    }

    // ---------------------------------------------------------------

    public function testGetPropertiesReturnsExpectedProperties()
    {
        $obj = $this->getObject(Tick::SKIP, 'hi', ['foo' => 'bar']);

        $this->assertEquals('hi',                      $obj->getMessage());
        $this->assertEquals(Tick::SKIP,                $obj->getStatus());
        $this->assertEquals(['foo' => 'bar'],          $obj->getExtraInfo());
        $this->assertInternalType('float',             $obj->getTimestamp());
        $this->assertInstanceOf('\TaskTracker\Report', $obj->getReport());
    }

    // ---------------------------------------------------------------

    protected function getObject($status = Tick::SUCCESS, $message = '', array $extra = [])
    {
        $tracker = \Mockery::mock('\TaskTracker\Tracker');
        $tracker->shouldReceive('getStartTime')->andReturn(100);
        $tracker->shouldReceive('getNumTotalItems')->andReturn(25);
        $tracker->shouldReceive('getLastTick')->andReturn(null);
        $tracker->shouldReceive('getNumProcessedItems')->andReturn(3);

        return new Tick($tracker, $status, $message, $extra);
    }
}
