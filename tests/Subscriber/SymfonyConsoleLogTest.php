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

use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use TaskTracker\Events;
use TaskTracker\Subscriber\SymfonyConsoleLog;
use TaskTracker\Test\Fixture\TickBuilderTrait;
use TaskTracker\Tick;
use TaskTracker\Tracker;

class SymfonyConsoleLogTest extends \PHPUnit_Framework_TestCase
{
    use TickBuilderTrait;

    public function testGetSubscribedEvents()
    {
        $expected = [
            Events::TRACKER_START  => 'writeStartLine',
            Events::TRACKER_TICK   => 'writeLogLine',
            Events::TRACKER_FINISH => 'writeFinishLine',
            Events::TRACKER_ABORT  => 'writeAbortLine'
        ];

        $this->assertEquals($expected, SymfonyConsoleLog::getSubscribedEvents());
    }

    /**
     * @dataProvider startFinishAbortMessagesProvider
     */
    public function testStartFinishAbortMessages($method, Tick $tick, $expected)
    {
        $output = new BufferedOutput();
        $obj = new SymfonyConsoleLog($output);

        call_user_func([$obj, $method], $tick);

        $this->assertEquals($expected, trim($output->fetch()));
    }

    public function testTickStandardVerbosity()
    {
        $output = new BufferedOutput();
        $obj = new SymfonyConsoleLog($output);

        $obj->writeLogLine($this->getTick());
        $this->assertEquals('SUCC» [3/25] Processing item 3', trim($output->fetch()));
    }

    /**
     * If the tracker is tracking an unknown number of items, ensure
     * that the number of items displayed is "[n]" instead of "[n/-1]"
     */
    public function testOutputDisplaysNumItemsCorrectlyForUnknownNumberOfTicks()
    {
        $tracker = \Mockery::mock('\TaskTracker\Tracker');
        $tracker->shouldReceive('getStartTime')->andReturn(100);
        $tracker->shouldReceive('getNumTotalItems')->andReturn(Tracker::UNKNOWN);
        $tracker->shouldReceive('getLastTick')->andReturn(null);
        $tracker->shouldReceive('getNumProcessedItems')->andReturn(3);
        $tick = new Tick($tracker, Tick::SUCCESS, 'msg', []);

        $output = new BufferedOutput();
        $obj = new SymfonyConsoleLog($output);

        $obj->writeLogLine($tick);
        $this->assertEquals('SUCC» [3] msg', trim($output->fetch()));
    }

    public function testTickVeryVerbose()
    {
        $output = new BufferedOutput();
        $output->setVerbosity(OutputInterface::VERBOSITY_VERBOSE);

        $obj = new SymfonyConsoleLog($output);

        $obj->writeLogLine($this->getTick(Tick::FAIL));

        $this->assertRegExp(
            '/\FAIL» \[3\/25\] [\d,]+:[\d]{2}:[\d]{2} \(3\/3\/3\) Processing item 3/',
            trim($output->fetch())
        );
    }

    public function testTickVeryVeryVerbose()
    {
        $output = new BufferedOutput();
        $output->setVerbosity(OutputInterface::VERBOSITY_VERY_VERBOSE);

        $obj = new SymfonyConsoleLog($output);

        $obj->writeLogLine($this->getTick(Tick::SKIP));

        $this->assertRegExp(
            '/SKIP» \[3\/25\] [\d,]+:[\d]{2}:[\d]{2} \(3\/3\/3\) \{[\d\w\.]+\/[\d\w\.]+\} Processing item 3/',
            trim($output->fetch())
        );

    }

    public function testSetCustomMapGeneratesExpectedOutput()
    {
        $output = new BufferedOutput();
        $obj = new SymfonyConsoleLog($output);

        $obj->setLinePrefixMap([
            Tick::SKIP    => '*',
            Tick::SUCCESS => '>',
            Tick::FAIL    => 'X'
        ]);

        $obj->writeLogLine($this->getTick(Tick::SUCCESS));
        $obj->writeLogLine($this->getTick(TICK::FAIL));
        $obj->writeLogLine($this->getTick(Tick::SKIP));

        $expected = "> [3/25] Processing item 3\nX [3/25] Processing item 3\n* [3/25] Processing item 3";
        $this->assertEquals($expected, trim($output->fetch()));
    }


    public function startFinishAbortMessagesProvider()
    {
        return array(
            ['writeStartLine',  $this->getTick(Tick::SUCCESS, 'Hello World'),   'Hello World'   ],
            ['writeStartLine',  $this->getTick(Tick::SUCCESS, ''),              'Starting . . .'],
            ['writeFinishLine', $this->getTick(Tick::SUCCESS, 'Bye Bye World'), 'Bye Bye World' ],
            ['writeFinishLine', $this->getTick(Tick::SUCCESS, ''),              '. . . Finished'],
            ['writeAbortLine',  $this->getTick(Tick::SUCCESS, 'Whoops'),        'Whoops'        ],
            ['writeAbortLine',  $this->getTick(Tick::SUCCESS, ''),              'Aborted!'      ],
        );
    }
}
