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

namespace TaskTracker;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Tracker Factory Service Class
 *
 * Provides a service library for building many Tracker objects
 * with the same set of subscribers
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class TrackerFactory
{
    /**
     * @var array<EventSubscriberInterface>
     */
    private array $defaultSubscribers;

    /**
     * Constructor
     *
     * @param array<EventSubscriberInterface> $defaultSubscribers
     */
    public function __construct(array $defaultSubscribers = [])
    {
        $this->defaultSubscribers = $defaultSubscribers;
    }

    /**
     * Build a new Tracker instance
     *
     * If the $extraSubscribers value is not empty, those subscribers will be added
     * to the Tracker in addition to the defaults.
     *
     * @param int                              $numItems          The total number of items (or -1 for unknown)
     * @param array<EventSubscriberInterface>  $extraSubscribers  Optionally specify extra listeners for this Tracker instance
     */
    public function buildTracker(int $numItems = Tracker::UNKNOWN, array $extraSubscribers = []): Tracker
    {
        $tracker = new Tracker($numItems);

        foreach (array_merge($this->defaultSubscribers, $extraSubscribers) as $listener) {
            $tracker->getDispatcher()->addSubscriber($listener);
        }

        return $tracker;
    }
}
