<?php

use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use TaskTracker\Subscriber\SymfonyConsoleLog;
use TaskTracker\Tracker;
use TaskTracker\Tick;
use TaskTracker\Subscriber\SymfonyConsoleProgress;
use TaskTracker\Subscriber\Psr3Logger;
use Monolog\Logger as MonologLogger;

require_once __DIR__ . '/vendor/autoload.php';

// Setup subscribers
$subscribers = [
    new SymfonyConsoleProgress(new ConsoleOutput(OutputInterface::VERBOSITY_DEBUG)),
    // new SymfonyConsoleLog(new ConsoleOutput(OutputInterface::VERBOSITY_VERY_VERBOSE)),
    new Psr3Logger(new MonologLogger('logger'))
];

// Setup a tracker for a job with 100 items
$tracker = Tracker::build($subscribers, 500);

$tracker->start("Let's go");
for ($i = 0; $i < 500; $i++) {
    usleep(10000);
    $tracker->tick();
}
$tracker->finish("All done");