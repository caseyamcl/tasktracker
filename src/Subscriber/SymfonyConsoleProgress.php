<?php

namespace TaskTracker\Subscriber;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use TaskTracker\Events;
use TaskTracker\Helper\BytesToHumanTrait;
use TaskTracker\Tick;

/**
 * Symfony Console Progress Meter Listener
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class SymfonyConsoleProgress implements EventSubscriberInterface
{
    use BytesToHumanTrait;

    // ---------------------------------------------------------------

    /**
     * @var OutputInterface;
     */
    private $output;

    /**
     * @var ProgressBar|null
     */
    private $progressBar;

    // --------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::TRACKER_START  => 'start',
            Events::TRACKER_TICK   => 'tick',
            Events::TRACKER_FINISH => 'finish',
            Events::TRACKER_ABORT  => 'abort'
        ];
    }

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

    // ---------------------------------------------------------------

    public function start(Tick $tick)
    {
        if ($tick->getMessage()) {
            $this->output->writeln($tick->getMessage());
        }

        $this->progressBar = new ProgressBar(
            $this->output,
            $tick->getReport()->getTotalItemCount() > -1
                ? $tick->getReport()->getTotalItemCount()
                : 0
        );

        $this->progressBar->start();
        $this->progressBar->setProgress(0);
    }

    // ---------------------------------------------------------------

    public function tick(Tick $tick)
    {
        $rpt = $tick->getReport();


        $msgSegs = [$tick->getMessage()];

        if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $msgSegs[] = sprintf('Processed: <fg=green>%s</fg=green>', number_format($rpt->getNumItemsSuccess(), 0));
            $msgSegs[] = sprintf('Skipped: <fg=yellow>%s</fg=yellow>', number_format($rpt->getNumItemsFail(), 0));
            $msgSegs[] = sprintf('Failed: <fg=red>%s</fg=red>'       , number_format($rpt->getNumItemsSkip(), 0));
        }

        if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {

            $msgSegs[] = sprintf("Avg: %s", number_format($rpt->getAvgItemTime(), 2));

            $msgSegs[] = sprintf(
                'Memory: %s/%s',
                $this->bytesToHuman($rpt->getMemUsage(),   2),
                $this->bytesToHuman($rpt->getMemPeakUsage(), 2)
            );
        }

        $this->progressBar->setMessage(implode(' | ', $msgSegs));
        $this->progressBar->advance($rpt->getTick()->getIncrementBy());
    }


    public function finish(Tick $tick)
    {
        $this->progressBar->finish();

        if ($tick->getMessage()) {
            $this->output->writeln($tick->getMessage());
        }

    }

    public function abort(Tick $tick)
    {
        $this->progressBar->clear();
        $this->output->writeln($tick->getMessage() ?: 'Aborted');
    }
}

/* EOF: SymfonyConsoleProgress.php */