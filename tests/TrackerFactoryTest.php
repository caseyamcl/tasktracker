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

namespace TaskTracker\Test;

use PHPUnit_Framework_TestCase;
use TaskTracker\Test\Fixture\ArrayRecordingSubscriber;
use TaskTracker\Tick;
use TaskTracker\TrackerFactory;

/**
 * Class TrackerFactoryTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class TrackerFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testInstantiateSucceeds()
    {
        $obj = new TrackerFactory([]);
        $this->assertInstanceOf('\TaskTracker\TrackerFactory', $obj);
    }

    // ---------------------------------------------------------------

    public function testBuildWithSubscribersWorks()
    {
        $sub = new ArrayRecordingSubscriber();
        $obj = new TrackerFactory([$sub]);
        $tracker = $obj->buildTracker();

        $tracker->tick(Tick::SUCCESS, 'msg1');
        $tracker->tick(Tick::SUCCESS, 'msg2');

        $this->assertEquals(['msg1', 'msg2'], $sub->getItems());
    }

    // ---------------------------------------------------------------

    public function testBuildWithExtraSubscribersUsesBothDefaultsAndExtras()
    {
        $subA = new ArrayRecordingSubscriber();
        $subB = new ArrayRecordingSubscriber();

        $obj = new TrackerFactory([$subA]);
        $tracker = $obj->buildTracker(10, [$subB]);

        $tracker->tick(Tick::SUCCESS, 'msg1');
        $tracker->tick(Tick::SUCCESS, 'msg2');

        $this->assertEquals(['msg1', 'msg2'], $subA
            ->getItems());
        $this->assertEquals(['msg1', 'msg2'], $subB->getItems());
    }
}
