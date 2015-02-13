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
 * Report Interface
 *
 * @package TaskTracker
 */
interface ReportInterface extends TickInterface
{

    /**
     * @return float
     */
    function getTimeStarted();

    /**
     * @return int
     */
    function getTotalItemCount();

    /**
     * @return Tick
     */
    function getTick();

    /**
     * @return int
     */
    function getNumItemsProcessed();

    /**
     * @return float
     */
    function getTimeElapsed();

    /**
     * @return int
     */
    function getNumItemsSuccess();

    /**
     * @return int
     */
    function getNumItemsFail();

    /**
     * @return int
     */
    function getNumItemsSkip();

    /**
     * @return float
     */
    function getItemTime();

    /**
     * @return float
     */
    function getMaxItemTime();

    /**
     * @return float
     */
    function getMinItemTime();

    /**
     * @return float
     */
    function getAvgItemTime();
}
