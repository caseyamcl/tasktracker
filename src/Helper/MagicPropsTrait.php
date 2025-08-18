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

namespace TaskTracker\Helper;

use ReflectionMethod;

/**
 * Adds magic methods to classes to provide direct access to
 * any property that has a 'getX()' interface method
 *
 * Mainly for convenience
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
trait MagicPropsTrait
{
    /**
     * @param string $item
     * @return mixed
     */
    public function __get(string $item)
    {
        return $this->__isset($item) ? call_user_func($this->getMethodCallback($item)) : null;
    }

    // ---------------------------------------------------------------

    /**
     * @param string $item
     * @return bool
     */
    public function __isset(string $item)
    {
        return is_callable($this->getMethodCallback($item));
    }

    // ---------------------------------------------------------------

    public function toArray(): array
    {
        $outArr = [];

        foreach (get_class_methods(__CLASS__) as $mthd) {

            $ref = new ReflectionMethod(__CLASS__, $mthd);

            if ($ref->isPublic() && str_starts_with($mthd, 'get')) {
                $propName = strtolower($mthd[3]) . substr($mthd, 4);
                $outArr[$propName] = call_user_func([$this, $mthd]);
            }
        }

        return $outArr;
    }

    // ---------------------------------------------------------------

    /**
     * Get Method Callback
     *
     * @param string $item
     * @return array  Callback
     */
    private function getMethodCallback(string $item): array
    {
        return [$this, 'get' . ucfirst($item)];
    }
}
