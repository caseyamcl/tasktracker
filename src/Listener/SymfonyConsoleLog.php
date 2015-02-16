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

namespace TaskTracker\Listener;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use TaskTracker\Events;
use TaskTracker\Helper\BytesToHumanTrait;
use TaskTracker\Helper\TimeFormatterTrait;
use TaskTracker\Tick;

/**
 * Symfony Console Log
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class SymfonyConsoleLog implements EventSubscriberInterface
{
    use BytesToHumanTrait;
    use TimeFormatterTrait;

    // --------------------------------------------------------------

    /**
     * @var OutputInterface
     */
    private $output;

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
     * Write Log Line
     *
     * @param Tick $tick
     * @param bool $isErr
     */
    public function writeLogLine(Tick $tick, $isErr = false)
    {
        // Line segments
        $lineSegs = array();

        // 1st Segment is a star
        if ($isErr) {
            $lineSegs[] = "<fg=red>*</fg=red>";
        }
        else {
            switch ($tick->getStatus()) {
                case Tick::SUCCESS:
                    $lineSegs[] = "<fg=green>*</fg=green>";
                    break;
                case Tick::FAIL:
                    $lineSegs[] = "<fg=red>*</fg=red>";
                    break;
                case Tick::SKIP:default:
                $lineSegs[] = "*";
            }
        }

        // Item Progress
        $lineSegs[] = sprintf(
            "[%s%s]",
            $tick->getReport()->getNumItemsProcessed(),
            $tick->getReport()->getTotalItemCount() ? "/" . $tick->getReport()->getTotalItemCount() : ''
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

        // If very verbose, add momory usage
        if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
            $lineSegs[] = sprintf("{%s/%s}",
                $this->bytesToHuman($tick->getReport()->getMemUsage()),
                $this->bytesToHuman($tick->getReport()->getMemPeakUsage())
            );
        }

        // Add message
        $lineSegs[] = $tick->getMessage() ?: sprintf(
            "Processing item %s",
            number_format($tick->getReport()->getTotalItemCount(), 0)
        );

        // Output it!
        $this->output->writeln(implode(' ', $lineSegs));
    }

    // ---------------------------------------------------------------

    public function writeStartLine(Tick $tick)
    {
        $this->output->writeln($tick->getMessage() ?: "Starting . . .");
    }

    public function writeFinishLine(Tick $tick)
    {
        $this->output->writeln($tick->getMessage() ?: ". . . Finished");
    }

    public function writeAbortLine(Tick $tick)
    {
        $this->output->writeln('<error>' . $tick->getMessage() ?: 'Aborted!' . '</error>');
    }
}
