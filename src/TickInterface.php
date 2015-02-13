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

namespace TaskTracker;

/**
 * Tick Interface
 *
 * @package TaskTracker
 */
interface TickInterface
{
    /**
     * Get message
     *
     * @return string
     */
    public function getMessage();

    /**
     * Get timestamp (microtime float)
     * @return float
     */
    public function getTimestamp();

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus();

    /**
     * Get incrementBy (in numbers)
     *
     * @return int
     */
    public function getIncrementBy();

    /**
     * @return Report
     */
    public function getReport();

    /**
     * @return array
     */
    public function getExtraInfo();
}
