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

declare(strict_types=1);

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

    private string $someVal;

    private string $anotherVal;

    // ---------------------------------------------------------------

    public function __construct()
    {
        $this->someVal    = 'fiz';
        $this->anotherVal = 'buzz';
    }

    // ---------------------------------------------------------------

    public function getSomeVal(): string
    {
        return $this->someVal;
    }

    public function getValWithoutAProperty(): string
    {
        return 'bar';
    }
}
