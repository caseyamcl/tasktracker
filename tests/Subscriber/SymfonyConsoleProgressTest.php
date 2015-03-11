<?php
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

namespace TaskTracker\Test\Subscriber;

use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use TaskTracker\Events;
use TaskTracker\Subscriber\SymfonyConsoleProgress;
use TaskTracker\Test\Fixture\TickBuilderTrait;

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

    public function testTickStandardVerbosity()
    {
        $output = new BufferedOutput();
        $obj = new SymfonyConsoleProgress($output);

        $obj->tick($this->getTick());

        var_dump($output->fetch());
    }

    // ---------------------------------------------------------------

    public function testTickVeryVerbose()
    {
        $output = new BufferedOutput();
        $output->setVerbosity(OutputInterface::VERBOSITY_VERBOSE);

        $obj = new SymfonyConsoleProgress($output);

        $obj->tick($this->getTick());

        var_dump($output->fetch());
    }

    // ---------------------------------------------------------------

    public function testTickVeryVeryVerbose()
    {
        $output = new BufferedOutput();
        $output->setVerbosity(OutputInterface::VERBOSITY_VERY_VERBOSE);

        $obj = new SymfonyConsoleProgress($output);

        $obj->tick($this->getTick());

        var_dump($output->fetch());
    }

}
