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

namespace TaskTracker\Test\Subscriber;

use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use TaskTracker\Events;
use TaskTracker\Subscriber\SymfonyConsoleProgress;
use TaskTracker\Test\Fixture\TickBuilderTrait;
use TaskTracker\Tick;

/**
 * Symfony Console Progress Test
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class SymfonyConsoleProgressTest extends \PHPUnit_Framework_TestCase
{
    use TickBuilderTrait;

    // ---------------------------------------------------------------

    public function testInstantiateSucceeds()
    {
        $obj = new SymfonyConsoleProgress(new BufferedOutput());
        $this->assertInstanceOf('\TaskTracker\Subscriber\SymfonyConsoleProgress', $obj);
    }

    // ---------------------------------------------------------------

    public function testGetSubscribedEvents()
    {
        $expected = [
            Events::TRACKER_START  => 'start',
            Events::TRACKER_TICK   => 'tick',
            Events::TRACKER_FINISH => 'finish',
            Events::TRACKER_ABORT  => 'abort'
        ];

        $this->assertEquals($expected, SymfonyConsoleProgress::getSubscribedEvents());
    }

    // ---------------------------------------------------------------

    public function testStartBuildsProgressBar()
    {
        $output = new BufferedOutput();
        $obj = new SymfonyConsoleProgress($output);

        $obj->start($this->getTick());

        $this->assertEquals("0/25 [>---------------------------]   0%", trim($output->fetch()));
    }

    // ---------------------------------------------------------------

    public function testFinishMessage()
    {
        $output = new BufferedOutput();
        $obj = new SymfonyConsoleProgress($output);

        $obj->start($this->getTick());
        $output->fetch();
        $obj->finish($this->getTick(Tick::SUCCESS, 'Done'));

        $expected = "25/25 [============================] 100%\nDone";
        $this->assertEquals(trim($expected), trim($output->fetch()));
    }

    // ---------------------------------------------------------------

    public function testAbortMessage()
    {
        $output = new BufferedOutput();
        $obj = new SymfonyConsoleProgress($output);

        $obj->start($this->getTick());
        $output->fetch();
        $obj->abort($this->getTick());

        $this->assertEquals('Aborted', trim($output->fetch()));
    }

    // ---------------------------------------------------------------

    public function testTickStandardVerbosity()
    {
        $output = new BufferedOutput();
        $obj = new SymfonyConsoleProgress($output);

        $obj->tick($this->getTick(Tick::SUCCESS, 'Test Message'));

        $expected = "Test Message\n  0/25 [>---------------------------]   0%\n  3/25 [===>------------------------]  12%";
        $this->assertEquals(trim($expected), trim($output->fetch()));
    }

    // ---------------------------------------------------------------

    public function testTickVeryVerbose()
    {
        $output = new BufferedOutput();
        $output->setVerbosity(OutputInterface::VERBOSITY_VERBOSE);

        $obj = new SymfonyConsoleProgress($output);

        $obj->tick($this->getTick(Tick::SUCCESS, 'Test Message'));

        $this->assertRegExp(
            '/Test Message\n(\s+)?0\/25\s+\[>(-+?)\]\s+0%\s+[\<\d\s]+sec(\s+)?\n(\s+)?3\/25\s+\[=+>(-+?)\]\s+12%\s+[\<\d\s]+sec(\s+)?/s',
            trim($output->fetch())
        );
    }

    // ---------------------------------------------------------------

    public function testTickVeryVeryVerbose()
    {
        $output = new BufferedOutput();
        $output->setVerbosity(OutputInterface::VERBOSITY_VERY_VERBOSE);

        $obj = new SymfonyConsoleProgress($output);

        $obj->tick($this->getTick(Tick::SUCCESS, 'Test Message'));

        $this->assertRegExp(
            '/Test Message\n(\s+)?0\/25\s+\[>(-+?)\]\s+0%\s+[\<\d\s]+sec(\s+)?\/[\<\d\s]+sec\n(\s+)?3\/25\s+\[=+>(-+?)\]\s+12%\s+[\<\d\s]+sec(\s+)?\/[\<\d\s]+sec/s',
            trim($output->fetch())
        );
    }

}
