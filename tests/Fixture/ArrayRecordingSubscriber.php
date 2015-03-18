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

namespace TaskTracker\Test\Fixture;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use TaskTracker\Events;
use TaskTracker\Tick;

class ArrayRecordingSubscriber implements EventSubscriberInterface
{
    /**
     * @var array
     */
    private $items;

    // ---------------------------------------------------------------

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->items = array();
    }

    // ---------------------------------------------------------------

    /**
     * @param Tick $tick
     */
    public function addToArray(Tick $tick)
    {
        $this->items[] = $tick->getMessage();
    }

    // ---------------------------------------------------------------

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    // ---------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [ Events::TRACKER_TICK => 'addToArray' ];
    }
}
