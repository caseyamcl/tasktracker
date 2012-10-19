<?php

/*
 * A simple functional test that runs a short process
 */

use Symfony\Component\Console\Output\ConsoleOutput;
use TaskTracker\OutputHandler\SymfonyConsole;
use TaskTracker\Tracker;


//
// Setup
//
require('bootstrap.php');

$output = new ConsoleOutput();
$handlers = array(new SymfonyConsole($output));

//
// Finite Test
//
$output->writeln("Finite test...");
$tracker = new Tracker($handlers, 5);

for ($i = 0; $i < 5; $i++) {
    $tracker->tick(1, "At $i");
    sleep(1);
}

//
// Infinite Test
//
$output->writeln("Infinite test...");
$tracker = new Tracker($handlers);

for ($i = 0; $i < 6; $i++) {
    $tracker->tick(1, "At $i");
    sleep(1);
}

/* EOF: functest.php */