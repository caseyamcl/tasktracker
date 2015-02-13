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

namespace Helper;

use TaskTracker\Helper\TimeFormatterTrait;

/**
 * Time Formatter Trait Test
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class TimeFormatterTraitTest extends \PHPUnit_Framework_TestCase
{
    use TimeFormatterTrait;

    // ---------------------------------------------------------------

    /**
     * @dataProvider formatSecondsTestProvider
     */
    public function testFormatSecondsReturnsExpectedValues($elapsedTime, $microDecimals, $expected)
    {
        $this->assertEquals(
            $expected,
            $this->formatSeconds($elapsedTime, $microDecimals)
        );
    }

    // ---------------------------------------------------------------

    /**
     * @return array
     */
    public function formatSecondsTestProvider()
    {
        return array(
            [2345444,      0, '651:30:44'],
            [2340283230,   0, '650,078:40:30'],
            [923408208302, 0, '256,502,280:05:02'],
            [22,           0, '00:22'],
            [144,          0, '02:24']
        );
    }
}
