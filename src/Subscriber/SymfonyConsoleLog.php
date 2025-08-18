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

    private OutputInterface $output;

    /**
     * @var array
     */
    private array $linePrefixMap = [
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
    public static function getSubscribedEvents(): array
    {
        return [
            Events::TRACKER_START->value  => 'writeStartLine',
            Events::TRACKER_TICK->value   => 'writeLogLine',
            Events::TRACKER_FINISH->value => 'writeFinishLine',
            Events::TRACKER_ABORT->value  => 'writeAbortLine'
        ];
    }


    // ---------------------------------------------------------------

    /**
     * Set line prefix map
     *
     * @param array $prefixMap  [status => 'prefix']
     */
    public function setLinePrefixMap(array $prefixMap): void
    {
        foreach ($prefixMap as $status => $prefix) {
            $this->setLinePrefix($status, $prefix);
        }
    }

    // ---------------------------------------------------------------

    /**
     * Set the prefix for a given status
     *
     * @param int    $status  Tick::SUCCESS, Tick::FAIL, or Tick::SUCCESS
     * @param string $prefix
     */
    public function setLinePrefix(int $status, string $prefix): void
    {
        $this->linePrefixMap[$status] = $prefix;
    }

    // ---------------------------------------------------------------

    /**
     * Write Log Line
     *
     * @param Tick $tick
     */
    public function writeLogLine(Tick $tick): void
    {
        // Line segments
        $lineSegs = array();

        // 1st Segment is a star
        $lineSegs[] = match ($tick->getStatus()) {
            Tick::SUCCESS => sprintf("<fg=green>%s</fg=green>", $this->linePrefixMap[Tick::SUCCESS]),
            Tick::FAIL => sprintf("<fg=red>%s</fg=red>", $this->linePrefixMap[Tick::FAIL]),
            default => $this->linePrefixMap[Tick::SKIP],
        };

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

        // Add a message
        $lineSegs[] = $tick->getMessage() ?: sprintf(
            "Processing item %s",
            number_format($tick->getReport()->getNumItemsProcessed())
        );

        // Output it!
        $this->output->writeln(implode(' ', $lineSegs));
    }

    // ---------------------------------------------------------------

    public function writeStartLine(Tick $tick): void
    {
        $this->output->writeln($tick->getMessage() ?: "Starting . . .");
    }

    public function writeFinishLine(Tick $tick): void
    {
        $this->output->writeln($tick->getMessage() ?: ". . . Finished");
    }

    public function writeAbortLine(Tick $tick): void
    {
        $this->output->writeln('<error>' . ($tick->getMessage() ?: 'Aborted!') . '</error>');
    }
}
