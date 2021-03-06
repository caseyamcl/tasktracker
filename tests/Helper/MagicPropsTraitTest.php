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

namespace Helper;

use TaskTracker\Test\Fixture\MagicPropsObject;

/**
 * Class MagicPropsTraitTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class MagicPropsTraitTest extends \PHPUnit_Framework_TestCase
{

    public function testToArrayReturnsExpectedValues()
    {
        $obj = new MagicPropsObject();

        $this->assertEquals(
            ['someVal' => 'fiz', 'valWithoutAProperty' => 'bar'],
            $obj->toArray()
        );
    }

    // ---------------------------------------------------------------

    public function testGetMagicMethodReturnsValueForActualGetMethods()
    {
        $obj = new MagicPropsObject();

        $this->assertEquals('fiz', $obj->someVal);
        $this->assertEquals('bar', $obj->valWithoutAProperty);
    }

    // ---------------------------------------------------------------

    public function testGetMagicMeghodReturnsNullForNoGetMethods()
    {
        $obj = new MagicPropsObject();

        $this->assertNull($obj->notExists);
        $this->assertNull($obj->anotherVal);
    }
}
