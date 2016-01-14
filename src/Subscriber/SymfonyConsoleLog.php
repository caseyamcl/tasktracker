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

namespace TaskTracker\Subscriber;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use TaskTracker\Events;
use TaskTracker\Helper\BytesToHumanTrait;
use TaskTracker\Helper\TimeFormatterTrait;
use TaskTracker\Tick;
use TaskTracker\Tracker;

/**
 * Symfony Console Log Task Tracker Subscriber
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class SymfonyConsoleLog implements EventSubscriberInterface
{
    // Use traits to aid in output
    use BytesToHumanTrait;
    use TimeFormatterTrait;

    // --------------------------------------------------------------

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var array
     */
    private $linePrefixMap = [
        Tick::SKIP    => 'SKIP»',
        TICK::SUCCESS => 'SUCC»',
        TICK::FAIL    => 'FAIL»'
    ];

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    // --------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::TRACKER_START  => 'writeStartLine',
            Events::TRACKER_TICK   => 'writeLogLine',
            Events::TRACKER_FINISH => 'writeFinishLine',
            Events::TRACKER_ABORT  => 'writeAbortLine'
        ];
    }


    // ---------------------------------------------------------------

    /**
     * Set line prefix map
     *
     * @param array $prefixMap  [status => 'prefix']
     */
    public function setLinePrefixMap(array $prefixMap)
    {
        foreach ($prefixMap as $status => $prefix) {
            $this->setLinePrefix($status, $prefix);
        }
    }

    // ---------------------------------------------------------------

    /**
     * Set prefix for a given status
     *
     * @param int    $status  Tick::SUCCESS, Tick::FAIL, or Tick::SUCCESS
     * @param string $prefix
     */
    public function setLinePrefix($status, $prefix)
    {
        $this->linePrefixMap[$status] = $prefix;
    }

    // ---------------------------------------------------------------

    /**
     * Write Log Line
     *
     * @param Tick $tick
     */
    public function writeLogLine(Tick $tick)
    {
        // Line segments
        $lineSegs = array();

        // 1st Segment is a star
        switch ($tick->getStatus()) {
            case Tick::SUCCESS:
                $lineSegs[] = sprintf("<fg=green>%s</fg=green>", $this->linePrefixMap[Tick::SUCCESS]);
                break;
            case Tick::FAIL:
                $lineSegs[] = sprintf("<fg=red>%s</fg=red>", $this->linePrefixMap[Tick::FAIL]);
                break;
            case Tick::SKIP:
            default:
                $lineSegs[] = $this->linePrefixMap[Tick::SKIP];
        }

        // Item Progress
        $lineSegs[] = sprintf(
            "[%s%s]",
            $tick->getReport()->getNumItemsProcessed(),
            $tick->getReport()->getTotalItemCount() != Tracker::UNKNOWN ? "/" . $tick->getReport()->getTotalItemCount() : ''
        );

        // If verbose, add walltime and item counts
        if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $lineSegs[] = $this->formatSeconds($tick->getReport()->getTimeElapsed());

            $lineSegs[] = sprintf(
                '(<fg=green>%s</fg=green>/%s/<fg=red>%s</fg=red>)',
                $tick->getReport()->getNumItemsSuccess(),
                $tick->getReport()->getNumItemsSkip(),
                $tick->getReport()->getNumItemsFail()
            );
        }

        // If very verbose, add memory usage
        if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
            $lineSegs[] = sprintf("{%s/%s}",
                $this->bytesToHuman($tick->getReport()->getMemUsage()),
                $this->bytesToHuman($tick->getReport()->getMemPeakUsage())
            );
        }

        // Add message
        $lineSegs[] = $tick->getMessage() ?: sprintf(
            "Processing item %s",
            number_format($tick->getReport()->getNumItemsProcessed(), 0)
        );

        // Output it!
        $this->output->writeln(implode(' ', $lineSegs));
    }

    // ---------------------------------------------------------------

    /**
     * @param Tick $tick
     */
    public function writeStartLine(Tick $tick)
    {
        $this->output->writeln($tick->getMessage() ?: "Starting . . .");
    }

    /**
     * @param Tick $tick
     */
    public function writeFinishLine(Tick $tick)
    {
        $this->output->writeln($tick->getMessage() ?: ". . . Finished");
    }

    /**
     * @param Tick $tick
     */
    public function writeAbortLine(Tick $tick)
    {
        $this->output->writeln('<error>' . ($tick->getMessage() ?: 'Aborted!') . '</error>');
    }
}
