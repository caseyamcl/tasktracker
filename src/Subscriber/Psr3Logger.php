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

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use TaskTracker\Events;
use TaskTracker\Tick;

/**
 * PSR3-Compatible Logger Task Tracker Subscriber
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
readonly class Psr3Logger implements EventSubscriberInterface
{
    public function __construct(
        private LoggerInterface $logger
    ) {
    }

    // ---------------------------------------------------------------

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    // ---------------------------------------------------------------

    /**
     * Start event callback
     */
    public function start(Tick $tick): void
    {
        $this->logger->info($tick->getMessage() ?: 'Started', $tick->getReport()->toArray());
    }

    // ---------------------------------------------------------------

    /**
     * Tick callback
     */
    public function tick(Tick $tick): void
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
     */
    public function finish(Tick $tick): void
    {
        $this->logger->info($tick->getMessage() ?: 'Finished', $tick->getReport()->toArray());
    }

    // ---------------------------------------------------------------

    /**
     * Abort callback
     */
    public function abort(Tick $tick): void
    {
        $this->logger->warning($tick->getMessage() ?: 'Aborted', $tick->getReport()->toArray());
    }

    // ---------------------------------------------------------------

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
}
