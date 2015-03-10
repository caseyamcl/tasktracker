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
 * Converts bytes into a human-readable format
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
trait BytesToHumanTrait
{
    /**
     * Convert bytes to a human-readable format
     *
     * For example (int) 1024 becomes (string) "1.02KB"
     *
     * @param int $bytes
     * @param int $decimals
     * @return string
     */
    protected function bytesToHuman($bytes, $decimals = 2)
    {
        $map = [
            1000000000000 => 'TB',
            1000000000    => 'GB',
            1000000       => 'MB',
            1000          => 'KB',
            1             => 'B'
        ];

        foreach ($map as $val => $suffix) {
            if ($bytes >= $val) {
                return number_format($bytes / $val, $decimals) . $suffix;
            }
        }

        // Shouldn't make it here, but just in case
        return number_format($bytes, $decimals) . 'B';
    }
}
