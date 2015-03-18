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
     * @return string
     */
    public function formatSeconds($elapsedTime)
    {
        $seconds = floor($elapsedTime);
        $output = array();

        //Hours (only if $seconds > 3600)
        if ($seconds > 3600) {
            $hours    = floor($seconds / 3600);
            $seconds  = $seconds - (3600 * $hours);
            $output[] = number_format($hours, 0);
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

        return implode(':', $output);
    }
}
