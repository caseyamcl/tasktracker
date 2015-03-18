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

namespace Subscriber;

use Monolog\Handler\TestHandler;
use Monolog\Logger;
use TaskTracker\Events;
use TaskTracker\Subscriber\Psr3Logger;
use TaskTracker\Test\Fixture\TickBuilderTrait;

/**
 * Psr3 Logger Test
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class Psr3LoggerTest extends \PHPUnit_Framework_TestCase
{
    use TickBuilderTrait;

    // ---------------------------------------------------------------

    public function testGetSubscribedEvents()
    {
        $expected = [
            Events::TRACKER_START  => 'start',
            Events::TRACKER_TICK   => 'tick',
            Events::TRACKER_FINISH => 'finish',
            Events::TRACKER_ABORT  => 'abort'
        ];

        $this->assertEquals($expected, Psr3Logger::getSubscribedEvents());
    }

    // ---------------------------------------------------------------

    public function testStartWorks()
    {
        $this->callbackTest('start');
    }

    // ---------------------------------------------------------------

    public function testTickWorks()
    {
        $this->callbackTest('tick');
    }

    // ---------------------------------------------------------------

    public function testFinishWorks()
    {
        $this->callbackTest('finish');
    }

    // ---------------------------------------------------------------

    public function testAbortWorks()
    {
        $handler = $this->callbackTest('abort');
        $this->assertTrue($handler->hasWarningRecords());
    }

    // ---------------------------------------------------------------

    public function testGetLogger()
    {
        $handler = new TestHandler();
        $obj = new Psr3Logger(new Logger('test', [$handler]));
        $this->assertInstanceOf('\Psr\Log\LoggerInterface', $obj->getLogger());
    }

    // ---------------------------------------------------------------

    protected function callbackTest($method)
    {
        $handler = new TestHandler();

        $obj = new Psr3Logger(new Logger('test', [$handler]));
        call_user_func([$obj, $method], $this->getTick());

        $this->assertCount(1, $handler->getRecords());
        return $handler;

    }

}
