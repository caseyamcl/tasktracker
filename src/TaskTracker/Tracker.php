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
     * @var Report 
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
     * Add an outputter
     *
     * @param OutputHandler\OutputHandler $handler
     */
    public function addOutputHandler(Ouputter $handler)
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
        //Send it to the output handlers
        $report = $this->buildReport($msg, $count, $tickType);        
        $this->sendToOutputHandler('tick', $report);
    }

    // --------------------------------------------------------------    

    /**
     * Build a report and send it to the finish method in the output handlers
     *
     * @param string $msg
     */
    public function finish($msg)
    {
        //Send report to finish method in output handler
        $report = $this->buildReport($msg);
        $this->sendToOutputHandler('finish', $report);
    }

    // --------------------------------------------------------------    

    /**
     * Build a report and send it to the abort method in the output handlers
     *
     * @param string $msg
     */
    public function abort($msg)
    {
        $report = $this->buildReport($msg);
        $this->sendToOutputHandler('abort', $report);
    }

    // --------------------------------------------------------------    

    /**
     * Send a report to the output handlers
     *
     * @param string $method  Which method to call on the output handlers
     * @param Report $report  The report to send
     */
    private function sendToOutputHandler($method, Report $report)
    {
        array_map(function($obj) use ($report) {
            call_user_func(array($obj, $method), $report)
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
    protected function buildReport($msg = null, $increment = 1, $incType = self::SUCCESS)
    {
        //Get time
        $mircotime = microtime(true);

        //Build new report
        $report = new Report();
        $report->currMessage       = ($msg === null) ? $this->lastReport->currMessage : $msg;
        $report->currMemUsage      = memory_get_usage();
        $report->maxMemUsage       = memory_get_peak_usage();
        $report->timeTotal         = $microtime - $this->startTime;
        $report->timeSinceLastTick = $microtime - $this->lastReport->currentTime;
        $report->currentTime       = $microtime;
        $report->numTicks          = $this->lastReport->numTicks + 1;
        $report->numItems          = $this->lastReport->numItems + $increment;
        $report->numItemsSuccess   = $this->lastReport->numItemsSuccess;
        $report->numItemsWarn      = $this->lastreport->numItemsWarn;
        $report->numItemsFail      = $this->lastreport->numItemsFail;
        $report->numItemsSkip      = $this->lastreport->numItemsSkip;
        $report->avgTickTime       = $report->timeTotal / $report->numTicks;
        $report->maxTickTime       = max(array($report->timesinceLastTick, $this->lastReport->maxTickTime));
        $report->minTickTime       = max(array($report->timeSinceLastTick, $this->lastReport->minTickTime));
        $report->medianTickTime    = $report->maxTickTime / 2;
        $report->totalItems        = $this->totalItems;

        //Status of tick?
        switch($incType) {
            case self::FAIL: $report->numItemsFail++; break;
            case self::WARN: $report->numItemsWarn++; break;
            case self::SKIP: $report->numItemsSkip++; break;
            case self::SUCCESS: default:
                $report->numItemsSuccess++;
        }

        $this->lastReport = $report;
        return $report;
    }
}

/* EOF: TaskTracker.php */