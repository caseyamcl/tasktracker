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

namespace TaskTracker\Test\Fixture;

use TaskTracker\Helper\MagicPropsTrait;

/**
 * Fixture for MagicPropsTrait Test
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class MagicPropsObject
{
    use MagicPropsTrait;

    // ---------------------------------------------------------------

    /**
     * @var string
     */
    private $someVal;

    /**
     * @var string
     */
    private $anotherVal;

    // ---------------------------------------------------------------

    public function __construct()
    {
        $this->someVal    = 'fiz';
        $this->anotherVal = 'buzz';
    }

    // ---------------------------------------------------------------

    /**
     * @return string
     */
    public function getSomeVal()
    {
        return $this->someVal;
    }

    /**
     * @return string
     */
    public function getValWithoutAProperty()
    {
        return 'bar';
    }
}
