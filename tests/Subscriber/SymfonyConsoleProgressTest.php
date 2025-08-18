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

namespace TaskTracker\Test\Subscriber;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;
use TaskTracker\Events;
use TaskTracker\Subscriber\SymfonyConsoleProgress;
use TaskTracker\Test\Fixture\TickBuilderTrait;
use TaskTracker\Tick;

/**
 * Symfony Console Progress Test
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class SymfonyConsoleProgressTest extends TestCase
{
    use TickBuilderTrait;

    // ---------------------------------------------------------------

    public function testInstantiateSucceeds()
    {
        $obj = new SymfonyConsoleProgress(new BufferedOutput());
        $this->assertInstanceOf(SymfonyConsoleProgress::class, $obj);
    }

    // ---------------------------------------------------------------

    public function testGetSubscribedEvents()
    {
        $expected = [
            Events::TRACKER_START->value  => 'start',
            Events::TRACKER_TICK->value   => 'tick',
            Events::TRACKER_FINISH->value => 'finish',
            Events::TRACKER_ABORT->value  => 'abort'
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

        $expected = "Test Message\n  0/25 [>---------------------------]   0%";
        $this->assertEquals(trim($expected), trim($output->fetch()));
    }
}
