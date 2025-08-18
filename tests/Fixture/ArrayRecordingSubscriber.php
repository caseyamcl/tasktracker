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

namespace TaskTracker\Test\Fixture;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use TaskTracker\Events;
use TaskTracker\Tick;

class ArrayRecordingSubscriber implements EventSubscriberInterface
{
    private array $items;

    // ---------------------------------------------------------------

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->items = array();
    }

    // ---------------------------------------------------------------

    public function addToArray(Tick $tick)
    {
        $this->items[] = $tick->getMessage();
    }

    // ---------------------------------------------------------------


    /**
     * @return array<Tick>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    // ---------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [ Events::TRACKER_TICK->value => 'addToArray' ];
    }
}
