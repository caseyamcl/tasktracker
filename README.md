Task Tracker
============

A library for tracking long-running tasks in PHP (when a simple progress bar isn't enough).

There are many libraries for asynchronously executing long-running tasks in PHP, but there are few that
handle reporting and tracking of these tasks in a predictable way.  This is what Task Tracker does.

It takes snapshots of the state of your memory usage, time elapsed, and progress counts during the execution
of a task, and can send reports periodically to multiple output handlers (console, Monolog, database, etc).

For example, you may want to display a progress bar on the console during execution of a task, but also
send periodic snapshots of the state of the system to Monolog while a task is executing.  Using a single
Tracker object, you can accomplish both of these goals.

Task Tracker comes pre-bundled with a Symfony2 console output handler, but you can easily write your own.

Installation
------------

Install via Composer:

1. Modify your _composer.json_ file:

        require {
            ...
            'caseyamcl/tasktracker': 'dev-master'
        }
        
2. Run <var>composer.phar</var> update

Install manually:

1. Download the source from http://github.com/caseyamcl/tasktracker
2. Include the _src/TaskTracker_ folder in your code.  You can ignore the other files

Usage
-----

Decide which output handler(s) you wish to use.  For the example below, we'll use Monolog and
Symfony Conosle:

    //Create an array to hold handlers
    $handlers = array();
    
    //Setup a monolog logger object (see Monolog documentation)
    $monologObject = new Monolog\Logger();
    
    //Create a new Tasktracker monolog handler object
    $handlers[] = new TaskTracker\OutputHandler\Monolog($monologObject);
    
    //Setup a Symfony console output object
    $consoleOutput = new Symfony\Component\Console\Output\Output();
    
    //Create a new Tasktracker console object
    $handlers[] = new TaskTracker\OutputHandler\SymfonyConsole($consoleOutput);
    
    //Create a new task tracker
    $tracker = new TaskTracker\Tracker($handlers);

When we create our tracker, we can also specify if the task we're tracking has a finite number of items
to process by supplying a second parameter:

    //The task we are tracking has 100 items
    $tracker = new TaskTracker\Tracker($handlers, 100);

Now we can trigger our tracker to create a report by using the <code>tick()</code> method:

    //Inform the tracker that we've completed processing two additional tiems
    $tracker->tick(2);
    
We can also supply an optional message

    $tracker->tick(2, "I just completed two more items!");
    
Finally, we can supply a third argument if we wish to indicate if the items failed, succeeded, or were
skipped:

    $tracker->tick(2, "Skipping some items, but continuing along.", TaskTracker\Tracker::SKIP)
    
    //Available constants are:
    // TaskTracker\Tracker::SUCCESS (default)
    // TaskTracker\Tracker::SKIP
    // TaskTracker\Tracker::FAIL
    
The <var>tick()</var> method will send a report to the output handlers we defined upon construction,
and those output handlers can decide what to do with it.  In this example, the Monolog handler will
log a report as Monolog is configured, and the Symfony console output handler will output a nice
looking progress report to the console.

The tracker class also includes <code>finish()</code> and <code>abort()</code> methods to indicate when
a task is done, or prematurely aborted.  They both accept optional messages.

    $tracker->abort("Aborting processing early for some reason");
    
    // ..or..
    
    $tracker->finish("All done!  Finish up.");
    
Writing your own Handler
------------------------

The included handlers are neat, but you will probably want to either extend them or write your own
handler.  This is done by creating a class that extends the <code>TaskTracker\OutputHandler\OutputHandler</code>
abstract class:

    class MyHandler extends TaskTracker\OutputHandler\OutputHandler
    {
       //Reuqired methods are:
       public function tick(TaskTracker\Report $report) {
         //Do something with the report 
       }
       
       public function abort(TaskTracker\Report $report) {
         //Do something with the report
       }
       
       public function finish(TaskTracker\Report $report) {
         //Do something with the report
       }
    }

The abstract <code>TaskTracker\OutputHandler\OutputHandler</code> class contains a single helper method
to format seconds into human-readable timestamps:

    public function tick(TaskTracker\Report $report) {
        
        //Pretty print the total time elapsed thus far
        $this->formatTime($report->timeTotal);
    }
    
The <var>Report</var> class is a simple value object that contains a bunch of nifty information about
the process and the state of the system during the last tick:

    class Report
    {
        const INFINITE = -1;
        
        /**
         * @var string  'Abort', 'Tick', or 'Finish'
         */
        public $action;
    
        /**
         * @var string
         */
        public $currMessage;
    
        /**
         * @var float
         */
        public $currMemUsage;
    
        /**
         * @var float
         */
        public $maxMemUsage;
    
        /**
         * @var float
         */
        public $startTime;
    
        /**
         * @var float  Current time
         */
        public $currentTime;
    
        /**
         * @var float  Number of seconds
         */
        public $timeTotal;
    
        /**
         * @var float  Number of seconds
         */
        public $timeSinceLastTick;
    
        /**
         * @var int  Total number of ticks so far
         */
        public $numTicks;
    
        /**
         * @var int  Total number of items
         */
        public $numItems;
    
        /**
         * @var int  Number of succeeded items
         */
        public $numItemsSuccess;
    
        /**
         * @var int  Number of warn items
         */
        public $numItemsWarn;
    
        /**
         * @var int  Number of failed items
         */
        public $numItemsFail;
    
        /**
         * @var int  Number of items skipped
         */
        public $numItemsSkip;
    
        /**
         * @var float  Number of seconds (avg time per tick)
         */
        public $avgTickTime;
    
        /**
         * @var float  In seconds
         */
        public $maxTickTime;
    
        /**
         * @var float  In seconds
         */
        public $minTickTime;
    
        /**
         * @var float  The median tick time
         */
        public $medianTickTime;
    
        /**
         * @var int  The number of total items (null for infinity)
         */
        public $totalItems;
    } 
