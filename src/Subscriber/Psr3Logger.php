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

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use TaskTracker\Events;
use TaskTracker\Tick;

/**
 * PSR3-Compatible Logger Task Tracker Subscriber
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class Psr3Logger implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    // ---------------------------------------------------------------

    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    // ---------------------------------------------------------------

    /**
     * Geth the logger
     *
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    // ---------------------------------------------------------------

    /**
     * Start callback
     *
     * @param Tick $tick
     */
    public function start(Tick $tick)
    {
        $this->logger->info($tick->getMessage() ?: 'Started', $tick->getReport()->toArray());
    }

    // ---------------------------------------------------------------

    /**
     * Tick callback
     *
     * @param Tick $tick
     */
    public function tick(Tick $tick)
    {
        $callback = ($tick->getStatus() == Tick::FAIL)
            ? [$this->logger, 'warning']
            : [$this->logger, 'info'];

        $msg = sprintf(
            "[%s/%s] %s",
            $tick->getReport()->getNumItemsProcessed(),
            $tick->getReport()->getTotalItemCount(),
            $tick->getMessage() ?: 'Tick'
        );

        call_user_func($callback, $msg, $tick->getReport()->toArray());
    }

    // ---------------------------------------------------------------

    /**
     * Finish callback
     *
     * @param Tick $tick
     */
    public function finish(Tick $tick)
    {
        $this->logger->info($tick->getMessage() ?: 'Finished', $tick->getReport()->toArray());
    }

    // ---------------------------------------------------------------

    /**
     * Abort callback
     *
     * @param Tick $tick
     */
    public function abort(Tick $tick)
    {
        $this->logger->warning($tick->getMessage() ?: 'Aborted', $tick->getReport()->toArray());
    }

    // ---------------------------------------------------------------

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
}
