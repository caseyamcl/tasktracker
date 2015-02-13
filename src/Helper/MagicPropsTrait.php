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

namespace TaskTracker\Helper;

/**
 * Magic Properties Trait
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
trait MagicPropsTrait
{
    /**
     * @param string $item
     * @return mixed
     */
    public function __get($item)
    {
        if ($this->__isset($item)) {
            return call_user_func($this->getMethodCallback($item));
        }
    }

    // ---------------------------------------------------------------

    /**
     * @param string $item
     * @return bool
     */
    public function __isset($item)
    {
        return is_callable($this->getMethodCallback($item));
    }

    // ---------------------------------------------------------------

    /**
     * @return array
     */
    public function toArray()
    {
        $outArr = [];

        foreach (get_class_methods(__CLASS__) as $mthd) {

            $ref = new \ReflectionMethod(__CLASS__, $mthd);

            if ($ref->isPublic() && substr($mthd, 0, 3) == 'get') {
                $propName = strtolower($mthd{3}) . substr($mthd, 4);
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
    private function getMethodCallback($item)
    {
        return [$this, 'get' . ucfirst($item)];
    }

}
