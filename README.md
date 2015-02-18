Task Tracker
============

[![Build Status](https://travis-ci.org/caseyamcl/tasktracker.png)](https://travis-ci.org/caseyamcl/tasktracker)

A library for tracking long-running tasks in PHP (when a simple progress bar isn't enough).

**At a Glance**:

* Reports on memory usage and a number of progress statistics during long-running tasks
* Useful for long-running processes where a large number of small jobs are executed
* Event-driven architecture using the [Symfony Event-Dispatcher Component](http://symfony.com/doc/current/components/event_dispatcher/introduction.html)
  - Can report on task progress to any [EventSubscriberInterface](http://symfony.com/doc/current/components/event_dispatcher/introduction.html#using-event-subscribers) objects
* Provides built-in utilities for reporting task progress:
  - Symfony Console Progress Bar
  - Symfony Console Running Log of Task Messages
  - Sending Task Progress to [PSR-3 Compatible Loggers](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md)

For example, you may want to display a progress bar on the console during execution of a task, but also
send periodic snapshots of the state of the system to Monolog while a task is executing.  Using a single
Tracker object, you can accomplish both of these goals:

    use Symfony\Console\Output\ConsoleOutput;
    use TaskTracker\Listener\SymfonyConsoleProgress
    use Monolog\Logger as MonologLogger;
    use TaskTracker\Tracker;
    use TaskTracker\Tick;

    // Setup listeners
    $listeners = [
        new SymfonyConsoleProgress(new ConsoleOutput()),
        new Psr3Logger(new MonologLogger())
    ];
    
    // Setup a tracker for a job with 100 items
    $tracker = new TaskTracker(100);
    $tracker->getDispatcher()->addListener();
    
    $tracker->start("Let's go");
    for ($i = 0; $i < 100; $i++) {
        // Do some work of some sort...
        $tracker->tick();
    }
    $tracker->finish("All done");


## Installation

Install via Composer:

1. Modify your _composer.json_ file:

        require {
            "caseyamcl/tasktracker": "~2.0"
        }
        
2. Run `composer update`

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
    $tracker->start('optional message');

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
    
And, you can increment by more than one item at a time:

    // Increment by 5 items
    $tracker->tick(Tick::SUCCESS, '', 5);
    
    // Increment by 3 items (skipped) with a message
    $trakcer->tick(Tick::FAIL, 'Something went wrong', 3);

When you are done, call the `Tracker::finish()` method:

    $tracker->finish('Optional finish manage');
    
Or, if things go wrong during processing, you can abort:

    $tracker->abort('Optional abort message');

The class contains a few helper methods, too:

    // Have we started processing yet?
    $tracker->isRunning();
    
    // Get the last tick, which contains all kinds of information
    $tracker->getLastTick();
    
    // Get the status of the process as an int (see class constants)
    $tracker->getStatus();
    
    // Get the number of items processed thus far
    $tracker->getNumProcessedItems();
    
    // Get only the number of failed items (works with SUCCESS and SKIP too)
    $tracker->getNumProcessedItems(Tick::FAIL);
    
    // Get the time started
    $tracker->getStartTime();
       
### Listeners

The `Tracker` class isn't very useful on its own without handlers to listen for
events.  There are a few built-in listeners:

* `TaskTracker\Listener\Psr3Logger` - Logs Tracker events to any [PSR-3 Logger](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md)
* `TaskTracker\Listener\SymfonyConsoleLog` - Logs Tracker events to a Symfony
   console, each event on its own line.
* `TaskTracker\Listener\SymfonyConsoleProgress` - Logs tracker events to a Symfony
   console progress bar indicator.
   
Using them is simple.  For example, suppose you have a Symfony Console Command, and
you want to show a progress bar and also log events as they occur:

    use TaskTracker\Tracker;
    use TaskTracker\Tick;
    use TaskTracker\Listener\SymfonyConsoleProgress;

    use Symfony\Component\Console\Command\Command;
    
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
        
            // A task tracker
            $tracker = new Tracker($numItems);
            
            // Setup a Progress Bar listener
            $tracker->getDispatcher()->addEventSubscriber(new SymfonyConsoleProgress($output));
            
            // Setup a Logger listener
            $monolog = new \Monolog\Logger(/* some handlers */);
            $tracker->getDispatcher()->addEventSubscriber(new Psr3Logger($monolog));
            
            // This is technically optional; if not called, it will automatically
            // be called on the first Tick
            $tracker->start("Let's go!");
            
            // The SymfonyConsoleProgress listener will output a progress bar
            for ($i = 0; $i < 10; $i++) {
                $tracker->tick(\Tick::SUCCESS, "On item: " . $i);
                sleep(1);
            }
            
            $tracker->finish('All done!');
        }
    }

    $tracker->getDispatcher()->addEventSubscriber();

### Custom Listeners

TaskTracker uses the [Symfony EventDispatcher](http://symfony.com/doc/current/components/event_dispatcher/introduction.html)
library, so any Symfony-compatible event listener can be used.

There are four events:

* `TaskTracker\Events::TRACKER_START`
* `TaskTracker\Events::TRACKER_TICK`
* `TaskTracker\Events::TRACKER_FINISH`
* `TaskTracker\Events::TRACKER_ABORT`

All four events dispatch an instance of the `TaskTracker\Task` class.  Your subscribers/listeners
should accept those as parameters:

    use Symfony\Component\EventDispatcher\EventSubscriberInterface;
    use TaskTracker\Tick;
    
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

