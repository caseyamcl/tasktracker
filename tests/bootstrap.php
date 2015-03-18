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

/*
 * Unit Tests Bootstrap File
 */

$autoloadFile = __DIR__.'/../vendor/autoload.php';

if (is_readable($autoloadFile)) {
    require_once($autoloadFile);
}
else {
    throw new RuntimeException('Install dependencies to run test suite (composer.phar install --dev).');
}

