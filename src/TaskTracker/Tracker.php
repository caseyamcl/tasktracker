<?php

namespace TaskTracker;

/**
 * The tracker class can be injected into long running
 * tasks and used to keep track of those tasks
 *
 * When triggered, it takes a snapshot of the system,
 * and gathers statistics on the task being run, and then
 * reports those stats to the designated OutputHandler objects
 */
class Tracker
{    
    const UNKNOWN = -1;

    /**
     * @var int  The number of total item (-1 for infinite/unknown)
     */
    private $totalItems;

    /**
     * @var array  Array of OuptutHandler Objects
     */
    private $outputHandlers = array();
    
    /**
     * @var Report  Holds the report
     */
    private $report;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param Array|OutputHandler $outputHandlers  Accepts an array or a single handler
     * @param int $totalItems                      Default is unknown (-1)
     */
    public function __construct($outputHandlers, $totalItems = self::UNKNOWN)
    {
        //Add the output handlers
        if ( ! is_array($outputHandlers)) {
            $outputHandlers = array($outputHandlers);
        }
        array_map(array($this, 'addOutputHandler'), $outputHandlers);

        $this->totalItems = $totalItems;
    }

    // --------------------------------------------------------------

    /**
     * Add an Outputhandler
     *
     * @param OutputHandler\OutputHandler $handler
     */
    public function addOutputHandler(OutputHandler\OutputHandler $handler)
    {
        $this->outputHandlers[] = $handler;
    }

    // --------------------------------------------------------------    

    /**
     * Start processing
     *
     * If this method is not called explicitely, it will automatically
     * be called upon first tick
     *
     * @param string $msg  Optional message to include
     */
    public function start($msg = null)
    {
        $this->report = new Report($this->totalItems);
        $this->sendToOutputHandlers('start', $msg);
    }

    // --------------------------------------------------------------

    /**
     * Build a report and send it to the tick method in the output handlers
     *
     * @param string $msg     Message to include for this report
     * @param int    $status  SUCCESS (default), SKIP, or FAIL 
     */
    public function tick($msg = null, $status = Tick::SUCCESS)
    {
        if ( ! $this->report) {
            $this->start();
        }

        $this->report->tick(new Tick($msg, $status));
        $this->sendToOutputHandlers('tick', $msg);
    }

    // --------------------------------------------------------------    

    /**
     * Build a report and send it to the finish method in the output handlers
     *
     * @param string $msg  Optional message to include
     */
    public function finish($msg = null)
    {
        if ( ! $this->report) {
            throw new \RuntimeException("Cannot finish taskTracker.  No processing has started");
        }

        $this->sendToOutputHandlers('finish', $msg);
    }

    // --------------------------------------------------------------    

    /**
     * Build a report and send it to the abort method in the output handlers
     *
     * @param string $msg  Optional message to include
     */
    public function abort($msg = null)
    {
        if ( ! $this->report) {
            throw new \RuntimeException("Cannot abort taskTracker.  No processing has started");
        }

        $this->sendToOutputHandlers('abort', $msg);
    }

    // --------------------------------------------------------------    

    /**
     * Send a report to the output handlers
     *
     * @param string $action  'start', 'tick', 'finish', 'abort'
     * @param string $message An optional message
     */
    private function sendToOutputHandlers($action, $message = '')
    {
        $report =& $this->report;

        array_map(function($obj) use ($action, $report, $message) {
            call_user_func(array($obj, $action), $report, $message);
        }, $this->outputHandlers);
    }
}

/* EOF: TaskTracker.php */