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
 * Time Formatter Trait
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
trait TimeFormatterTrait
{
    /**
     * Format Seconds into readable walltime (HH:ii:ss)
     *
     * @param float $elapsedTime
     * @param int   $decimals
     * @return string
     */
    public function formatSeconds($elapsedTime, $decimals = 0)
    {
        $seconds = floor($elapsedTime);
        $output = array();

        //Hours (only if $seconds > 3600)
        if ($seconds > 3600) {
            $hours    = floor($seconds / 3600);
            $seconds  = $seconds - (3600 * $hours);
            $output[] = $hours;
        }

        //Minutes
        if ($seconds >= 60) {
            $minutes  = floor($seconds / 60);
            $seconds  = $seconds - ($minutes * 60);
            $output[] = str_pad((string) $minutes, 2, '0', STR_PAD_LEFT);
        }
        else {
            $output[] = '00';
        }

        //Seconds
        $output[] =($seconds > 0)
            ? str_pad((string) $seconds, 2, '0', STR_PAD_LEFT)
            : '00';

        // Return string
        return sprintf(
            "%s%s",
            implode(":", $output),
            $decimals > 0 ? '.' . number_format($elapsedTime - $seconds, $decimals) : ''
        );

    }
}
