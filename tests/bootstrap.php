<?php

/**
 * @file TaskTracker PHPUnit Bootstrap File
 */

// ------------------------------------------------------------------

$autoloadFile = __DIR__.'/../vendor/autoload.php';

if (is_readable($autoloadFile)) {
    require_once($autoloadFile);
}
else {
    throw new RuntimeException('Install dependencies to run test suite (composer.phar install).');
}

/* EOF: bootstrap.php */