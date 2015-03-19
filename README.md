Task Tracker
============

**A library for tracking long-running tasks in PHP (when a simple progress bar isn't enough)**

[![Latest Version](https://img.shields.io/github/release/caseyamcl/tasktracker.svg?style=flat-square?style=flat-square)](https://github.com/caseyamcl/tasktracker/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://travis-ci.org/caseyamcl/tasktracker.png)](https://travis-ci.org/caseyamcl/tasktracker)

**At a Glance**:

* Reports on memory usage and a number of progress statistics during long-running tasks
* Useful for long-running processes where a large number of small jobs are executed
* Event-driven architecture using the [Symfony Event-Dispatcher Component](http://symfony.com/doc/current/components/event_dispatcher/introduction.html)
  - Can report on task progress to any [EventSubscriberInterface](http://symfony.com/doc/current/components/event_dispatcher/introduction.html#using-event-subscribers)
* Provides built-in utilities for reporting task progress:
  - Symfony Console Progress Bar
  - Symfony Console Running Log of Task Messages
  - Sending Task Progress to [PSR-3 Compatible Loggers](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md)

For example, you may want to display a progress bar on the console during execution of a task, but also
send periodic snapshots of the state of the system to Monolog while a task is executing.  Using a single
Tracker object, you can accomplish both of these goals:

```php

use TaskTracker\Tracker,
    TaskTracker\Tick;
use TaskTracker\Subscriber\SymfonyConsoleProgress,
    TaskTracker\Subscriber\Psr3Logger;
use Symfony\Console\Output\ConsoleOutput;
use Monolog\Logger as MonologLogger;


// Setup subscribers
$subscribers = [
    new SymfonyConsoleProgress(new ConsoleOutput()),
    new Psr3Logger(new MonologLogger())
];

// Setup a tracker for a job with 100 items
$tracker = Tracker::build(100, $subscribers);

$tracker->start("Let's go");
for ($i = 0; $i < 100; $i++) {
    // Do some work of some sort...
    $tracker->tick();
}
$tracker->finish("All done");
```

## Installation

Install via Composer:

    composer require caseyamcl/tasktracker:~2.0

Install manually:

1. Download the source from <http://github.com/caseyamcl/tasktracker>.
2. Include the `src/TaskTracker` folder in your code using a PSR-4 compatible autoloader.

Usage
-----

To track a task, create an instance of the `Tracker` class:

    use TaskTracker\Tracker;
    
    // Instantiate a tracker to track 100 items
    $tracker = new Tracker(100);

You can omit the number of items if you are working with an unknown number:

    $tracker = new Tracker();
    
The `Tracker` class creates its own `EventDispatcher`, but you can optionally
inject your own if you need to:

    $dispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();
    
    $tracker = new Tracker(100, $dispatcher);
    // ..or..
    $tracker = new Tracker(Tracker::UNKNOWN, $dispatcher);
    
To start tracking, simply call the `Tracker::start()` method:

    // Start the tracker
    $tracker->start('optional starting message');

For every element you process, call the `Tracker::tick()` method until you
are done:

    // Tick
    $tracker->tick();
    
There are three types of Ticks: *Success* (default), *Fail*, and *Skip*:

    use Tracker\Tick;

    $tracker->tick(Tick::SUCCESS);
    $tracker->tick(Tick::FAIL);
    $tracker->tick(Tick::SKIP);

You can also supply an optional message:

    $tracker->tick(Tick::SUCCESS, 'Things are going well.');
    $tracker->tick(Tick::FAIL,    'Crud.  Something went wrong');
    $tracker->tick(Tick::SKIP,    'Skipping this record for whatever reason');

You can add custom data to the Tick in the form of an array:

    $tracker->tick(Tick::SUCCESS, 'Things are going well.', ['foo' => 'bar', 'baz' => 'biz]);
    
And, you can increment by more than one item at a time:

    // Increment by 5 items
    $tracker->tick(Tick::SUCCESS, '', [], 5);
    
    // Three items failed
    $trakcer->tick(Tick::FAIL, 'Something went wrong', [], 3);

When you are done, call the `Tracker::finish()` method:

    $tracker->finish('Optional finish manage');
    
Or, if things go wrong during processing, you can abort:

    $tracker->abort('Optional abort message');

The class contains a few helper methods, too:

    // Have we started processing yet?
    $tracker->isRunning();
    
    // Get the last tick (instance of \Tracker\Tick class)
    $tracker->getLastTick();
    
    // Get the status of the process as an int (Tracker::NOT_STARTED, Tracker::RUNNING, Tracker::FINISHED, or Tracker::ABORTED)
    $tracker->getStatus();
    
    // Get the number of items processed thus far
    $tracker->getNumProcessedItems();
    
    // Get only the number of failed items (works with SUCCESS and SKIP too)
    $tracker->getNumProcessedItems(Tick::FAIL);
    
    // Get the time started (in microseconds)
    $tracker->getStartTime();
       
### Subscribers

The `Tracker` class isn't very useful on its own without event subscribers to listen for
tracker tick events.  There are a few subscribers bundled with this library:

* `TaskTracker\Subscriber\Psr3Logger` - Logs Tracker events to any [PSR-3 Logger](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md)
* `TaskTracker\Subscriber\SymfonyConsoleLog` - Logs Tracker events to a Symfony console, each event on its own line.
* `TaskTracker\Subscriber\SymfonyConsoleProgress` - Logs tracker events to a Symfony console progress bar indicator.

You can add event subscribers to the Tracker by calling the `Tracker::addSubscriber()` method:

    $tacker = new Tracker(100);
    $tracker->addSubscriber(new SymfonyConsoleLog($output));

If you know what subscribers you will use ahead of time, you can use the `Tracker::build()` method for convenience:
 
    $subscribers = [new SymfonyConsoleLog($output), new SomeOtherSubscriber()];
    $tracker = Tracker::build(100, $subscribers);

### Example

As an example, suppose you are creating a Symfony Console Command, and
you want to show a progress bar for some task and also log events as they occur:

```php
use TaskTracker\Tracker;
use TaskTracker\Tick;
use TaskTracker\Subscriber\SymfonyConsoleProgress;
use Symfony\Component\Console\Command\Command;

/**
 * My Symfony Command
 */
class MyCommand extends Command
{
    protected function configure()
    {
        $this->setName('example');
        $this->setDescription("Demonstrate TaskTracker");
    }
   
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $numItems = 10;
    
        // Build Task Tracker with Symfony Console Progress Bar subscriber
        $tracker = Tracker::build([new SymfonyConsoleProgress($output)], $numItems);
        
        // Add a Monolog Listener after Tracker construction
        $monolog = new \Monolog\Logger(/* some handlers */);
        $tracker->addSubscriber(new Psr3Logger($monolog));
        
        // You can also add Event Listeners directly
        $tracker->getDispatcher()->addListener(\Tracker\Events::TRACKER_TICK, function(\Tracker\Tick $tick) {
            // do something...
        });
        
        // Tracker::start() is technically optional; if not called, it will automatically
        // be called upon the first Tick
        $tracker->start("Let's go!");
        
        // The SymfonyConsoleProgress listener will output a progress bar while the logger will log events
        for ($i = 0; $i < 10; $i++) {
            $tracker->tick(\Tick::SUCCESS, "On item: " . $i);
            sleep(1);
        }
        
        // Tracker::start(), Tracker::tick(), Tracker::abort(), and Tracker::finish() all return
        // a \Tracker\Report object.
        $report = $tracker->finish('All done!');
        
        $output->writeln(sprintf("All Done!  <info>%s</info> items processed", $report->getNumTotalItems()));
    }
}
```

### Custom Subscribers

TaskTracker uses the [Symfony EventDispatcher](http://symfony.com/doc/current/components/event_dispatcher/introduction.html)
library, so any Symfony-compatible event listener can be used.

There are four events you can listen for:

* `TaskTracker\Events::TRACKER_START`
* `TaskTracker\Events::TRACKER_TICK`
* `TaskTracker\Events::TRACKER_FINISH`
* `TaskTracker\Events::TRACKER_ABORT`

All four events dispatch an instance of the `TaskTracker\Tick` class.  Your subscribers/listeners
should accept an object of that class as its parameter:

```php
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use TaskTracker\Tick;

/**
 * Listen for Tracker Events
 */
class MyEventSubscriber implements EventSubscriberInterface
{
     public static function getSubscribedEvents()
     {
         return [
             TaskTracker\Events::TRACKER_START  => 'handle',
             TaskTracker\Events::TRACKER_TICK   => 'handle',
             TaskTracker\Events::TRACKER_FINISH => 'handle',
         ];
     }
     
     public static function handle(Tick $tickEvent)
     {
         // See all of the information about the progress of that tick
         var_dump($tickEvent->getReport()->toArray());
     }
}
```

### Reports

Every Tracker event emits a `\Tracker\Report` object with a snapshot of the process and some system information
present at the point in time that the event occurred:

```php
$report = $tracker->tick();

$report->getTimeStarted();
$report->getTotalItemCount();
$report->getTick();
$report->getNumItemsProcessed();
$report->getTimeElapsed();
$report->getNumItemsSuccess();
$report->getNumItemsFail();
$report->getNumItemsSkip();
$report->getItemTime();
$report->getMaxItemTime();
$report->getMinItemTime();
$report->getAvgItemTime();
$report->getMemUsage();
$report->getMemPeakUsage();
$report->getMessage();
$report->getTimestamp();
$report->getStatus();
$report->getIncrementBy();
$report->getReport();
$report->getExtraInfo();
```

In your subscribers, you can access the report from the `Tick` object by calling `Tick::getReport()`.
