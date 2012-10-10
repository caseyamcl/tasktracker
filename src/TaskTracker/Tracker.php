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
    const SUCCESS  = 1;
    const FAIL     = 0;
    const WARN     = -1;
    const SKIP     = -2;

    const INFINITE = -1;

    // --------------------------------------------------------------

    /**
     * @var float  Microtimestamp
     */
    private $startTime;

    /**
     * @var Report  The previous report
     */
    private $lastReport;

    /**
     * @var array  Array of OuptutHandler Objects
     */
    private $outputHandlers = array();

    /**
     * @var int
     */
    private $totalItems;
    
    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param Array|OutputHandler $outputHandlers  Accepts an array or a single handler
     * @param int $totalItems                      Default is infinite (-1)
     */
    public function __construct($outputHandlers, $totalItems = self::INFINITE)
    {
        //Add the output handlers
        if ( ! is_array($outputHandlers)) {
            $outputHandlers = array($outputHandlers);
        }
        array_map(array($this, 'addOutputHandler'), $outputHandlers);
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
     * Build a report and send it to the tick method in the output handlers
     *
     * @param string $msg       Message to include for this report
     * @param int    $count     The amount to increment by
     * @param int    $tickType  SUCCESS (default), WARN, or FAIL 
     */
    public function tick($msg, $count = 1, $tickType = self::SUCCESS)
    {
        $report = $this->buildReport('tick', $msg, $count, $tickType);        
        $this->sendToOutputHandler($report);
    }

    // --------------------------------------------------------------    

    /**
     * Build a report and send it to the finish method in the output handlers
     *
     * @param string $msg
     */
    public function finish($msg)
    {
        $report = $this->buildReport('finish', $msg, 0);
        $this->sendToOutputHandler($report);
    }

    // --------------------------------------------------------------    

    /**
     * Build a report and send it to the abort method in the output handlers
     *
     * @param string $msg
     */
    public function abort($msg)
    {
        $report = $this->buildReport('abort', $msg, 0);
        $this->sendToOutputHandler($report);
    }

    // --------------------------------------------------------------    

    /**
     * Send a report to the output handlers
     *
     * @param string $method  Which method to call on the output handlers
     * @param Report $report  The report to send
     */
    private function sendToOutputHandler(Report $report)
    {
        array_map(function($obj) use ($report) {
            call_user_func(array($obj, $report->action), $report);
        }, $this->outputHandlers);
    }

    // --------------------------------------------------------------    

    /** 
     * Build a report
     *
     * @param string $msg     An optional message for the report
     * @param int $increment  Number to increment by
     * @param int $incType    SUCCESS (default), WARN, SKIP, or FAIL
     * @return Report
     */
    protected function buildReport($action = 'tick', $msg = null, $increment = 1, $incType = self::SUCCESS)
    {
        //Get time
        $microtime = microtime(true);

        //If first tick, start
        if ( ! $this->lastReport) {
            $this->start();
        }

        //Build new report
        $report = new Report();

        //Num ticks
        $report->numTicks = ($action == 'tick')
            ? $this->lastReport->numTicks + 1
            : $this->lastReport->numTicks;

        //Other data
        $report->action            = $action;
        $report->currMessage       = ($msg === null) ? $this->lastReport->currMessage : $msg;
        $report->currMemUsage      = memory_get_usage();
        $report->maxMemUsage       = memory_get_peak_usage();
        $report->startTime         = $this->startTime;        
        $report->timeTotal         = $microtime - $this->startTime;
        $report->timeSinceLastTick = $microtime - $this->lastReport->currentTime;
        $report->currentTime       = $microtime;
        $report->numItems          = $this->lastReport->numItems + $increment;
        $report->numItemsSuccess   = $this->lastReport->numItemsSuccess;
        $report->numItemsWarn      = $this->lastReport->numItemsWarn;
        $report->numItemsFail      = $this->lastReport->numItemsFail;
        $report->numItemsSkip      = $this->lastReport->numItemsSkip;
        $report->totalItems        = $this->totalItems;

        //Min and Max
        //If this is the first time, the tick time is 0
        if ($report->numTicks == 1) {
            $report->maxTickTime = 0;
            $report->minTickTime = 0;
        }
        //If this is the second time, we can provide a real time
        elseif ($report->numTicks == 2) {
            $report->maxTickTime = $report->timeSinceLastTick;
            $report->minTickTime = $report->timeSinceLastTick;
        }
        //After that, we can calculate
        else {
            $report->maxTickTime = max(array($this->lastReport->maxTickTime, $report->timeSinceLastTick));
            $report->minTickTime = min(array($this->lastReport->minTickTime, $report->timeSinceLastTick));
        }

        //Average and median
        $report->avgTickTime       = $report->timeTotal / $report->numTicks;
        $report->medianTickTime    = $report->maxTickTime / 2;


        //Status of tick?
        if ($increment > 0) {

            switch($incType) {
                case self::FAIL: $report->numItemsFail++; break;
                case self::WARN: $report->numItemsWarn++; break;
                case self::SKIP: $report->numItemsSkip++; break;
                case self::SUCCESS: default:
                    $report->numItemsSuccess++;
            }
        }

        //Update last report
        $this->lastReport = $report;

        //Return it
        return $report;
    }

    // --------------------------------------------------------------    

    /**
     * Start (executed on first tick)
     */
    protected function start()
    {
        //If no existing report report, start things up
        if ( ! $this->lastReport) {

            //Set start time
            $this->startTime = microtime(true);

            //Build starting report
            $this->lastReport = new Report();
            $this->lastReport->startTime = $this->startTime;
            $this->lastReport->currMessage = 'Processing';
            $this->lastReport->currentTime = $this->startTime;
            $this->lastReport->numTicks = 0;
            $this->lastReport->numItems = 0;
            $this->lastReport->numItemsSuccess = 0;
            $this->lastReport->numItemsWarn = 0;
            $this->lastReport->numItemsFail = 0;
            $this->lastReport->numItemsSkip = 0;
            $this->lastReport->maxTickTime = -1;
            $this->lastReport->minTickTime = -1;
        }
        else {
            throw new \RuntimeException("Cannot start task tracker.  Already started!");
        }
    }    
}

/* EOF: TaskTracker.php */