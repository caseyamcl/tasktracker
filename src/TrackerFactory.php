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

namespace TaskTracker;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Tracker Factory Service Class
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class TrackerFactory
{
    /**
     * @var array|EventSubscriberInterface[]
     */
    private $defaultListeners;

    // ---------------------------------------------------------------

    /**
     * Constructor
     *
     * @param EventSubscriberInterface[] $defaultListeners
     */
    public function __construct(array $defaultListeners = [])
    {
        $this->defaultListeners = $defaultListeners;
    }

    // ---------------------------------------------------------------

    /**
     * Get tracker for a task
     *
     * @param int                               $numItems      The total number of items (or -1 for unknown)
     * @param array|EventSubscriberInterface[]  $extraListeners  Optionally specify listeners, or will use defaults
     * @return Tracker
     */
    public function getTracker($numItems = Tracker::UNKNOWN, array $extraListeners = [])
    {
        $tracker = new Tracker($numItems);

        foreach (array_merge($this->defaultListeners, $extraListeners) as $listener) {
            $tracker->getDispatcher()->addSubscriber($listener);
        }

        return $tracker;
    }
}
