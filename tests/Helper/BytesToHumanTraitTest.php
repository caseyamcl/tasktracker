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

use TaskTracker\Helper\BytesToHumanTrait;

/**
 * Class BytesToHumanTraitTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class BytesToHumanTraitTest extends \PHPUnit_Framework_TestCase
{
    use BytesToHumanTrait;

    /**
     * @dataProvider testConversionWorksProvider
     */
    public function testConversionWorks($inputValue, $inputDecimals, $expected)
    {
        $this->assertEquals($this->bytesToHuman($inputValue, $inputDecimals), $expected);
    }

    // ---------------------------------------------------------------

    /**
     * Data provider for testConversionWorks
     */
    public function testConversionWorksProvider()
    {
        return array(
            [1429365116108.8,  2, '1.43TB'     ],
            [21721797099.52,   3, '21.722GB'   ],
            [1688849860263936, 3, '1,688.850TB'],
            [208666624,        2, '208.67MB'   ],
            [46080,            0, '46KB'       ],
            [101,              2, '101.00B'    ],
            [0,                0, '0B'         ]
        );
    }
}
