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

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use TaskTracker\Events;
use TaskTracker\Helper\BytesToHumanTrait;
use TaskTracker\Tick;

/**
 * Symfony Console Progress Bar Task Tracker Subscriber
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class SymfonyConsoleProgress implements EventSubscriberInterface
{
    // Use traits for prettier output
    use BytesToHumanTrait;

    // ---------------------------------------------------------------

    private OutputInterface $output;
    private ?ProgressBar $progressBar = null;

    // --------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::TRACKER_START->value  => 'start',
            Events::TRACKER_TICK->value   => 'tick',
            Events::TRACKER_FINISH->value => 'finish',
            Events::TRACKER_ABORT->value  => 'abort'
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

    /**
     * Start callback
     */
    public function start(Tick $tick): void
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
    }

    // ---------------------------------------------------------------

    /**
     * Tick callback
     */
    public function tick(Tick $tick): void
    {
        $rpt = $tick->getReport();

        if ( ! $this->progressBar) {
            $this->start($tick);
        }

        $this->progressBar->setProgress($rpt->getNumItemsProcessed());
    }

    /**
     * Finish callback
     */
    public function finish(Tick $tick): void
    {
        $this->progressBar->finish();

        if ($tick->getMessage()) {

            $this->output->writeln(PHP_EOL . $tick->getMessage());
        }

    }

    /**
     * Abort callback
     */
    public function abort(Tick $tick): void
    {
        $this->progressBar->clear();
        $this->output->writeln($tick->getMessage() ?: 'Aborted');
    }
}
