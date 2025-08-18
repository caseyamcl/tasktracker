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

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TaskTracker\Test\Fixture\TickBuilderTrait;
use TaskTracker\Tick;

/**
 * Class TickTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class TickTest extends TestCase
{
    use TickBuilderTrait;

    // ---------------------------------------------------------------

    public function testInstantiateTickSucceeds()
    {
        $obj =$this->getTick();
        $this->assertInstanceOf('\TaskTracker\Tick', $obj);
    }

    // ---------------------------------------------------------------

    public function testInstantiateWithInvalidStatusThrowsException()
    {
        $this->expectException('\InvalidArgumentException');
        $obj = $this->getTick(3);
    }

    // ---------------------------------------------------------------

    public function testGetPropertiesReturnsExpectedProperties()
    {
        $obj = $this->getTick(Tick::SKIP, 'hi', ['foo' => 'bar']);

        $this->assertEquals('hi', $obj->getMessage());
        $this->assertEquals(Tick::SKIP, $obj->getStatus());
        $this->assertEquals(['foo' => 'bar'], $obj->getExtraInfo());
        $this->assertIsFloat($obj->getTimestamp());
        $this->assertInstanceOf('\TaskTracker\Report', $obj->getReport());
    }

}
