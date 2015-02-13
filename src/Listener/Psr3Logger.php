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

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use TaskTracker\Events;
use TaskTracker\Tick;

/**
 * PSR3-Compatible Logger Output Listener
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

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    // ---------------------------------------------------------------

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    // ---------------------------------------------------------------

    public function start(Tick $tick)
    {
        $this->logger->info('Started', $tick->getReport()->toArray());
    }

    public function tick(Tick $tick)
    {
        $this->logger->info(sprintf(
           "%s %s/%s",
            $tick->getMessage() ?: 'Tick',
            $tick->getReport()->getNumItemsProcessed(),
            $tick->getReport()->getTotalItemCount()
        ), $tick->getReport()->toArray());
    }

    public function finish(Tick $tick)
    {
        $this->logger->info('Finished', $tick->getReport()->toArray());
    }

    public function abort(Tick $tick)
    {
        $this->logger->warning('Aborted', $tick->getReport()->toArray());
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
