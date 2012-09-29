<?php

namespace TaskTracker;

/**
 * The tracker class can be injected into long running
 * tasks and used to keep track of long running tasks
 *
 * When triggered, it takes a snapshot of the system,
 * and gathers statistics on the task being run, and then
 * reports them to a designated outputter objects
 */
class Tracker
{    
    const SUCCESS  = 1;
    const FAIL     = 0;
    const WARN     = -1;

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
     * @var array  Array of Outputter Objects
     */
    private $outputters = array();

    /**
     * @var int
     */
    private $totalItems;
    
    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param Array|Outputter $outputters  Accepts an array or a single outputter
     * @param int $totalItems              Default is infinite (-1)
     */
    public function __construct($outputters, $totalItems = self::INFINITE)
    {
        //Add the outputters
        if ( ! is_array($outputters)) {
            $outputters = array($outputters);
        }
        array_map(array($this, 'addOutputter'), $outputters);
    }

    // --------------------------------------------------------------

    /**
     * Add an outputter
     *
     * @param Outputter\Outputter $outputter
     */
    public function addOutputter(Ouputter $outputter)
    {
        $this->outputters[] = $outputter;
    }

    // --------------------------------------------------------------

    /**
     * Build a report and send it to the tick method in the outputters
     *
     * @param string $msg       Message to include for this report
     * @param int    $count     The amount to increment by
     * @param int    $tickType  SUCCESS (default), WARN, or FAIL 
     */
    public function tick($msg, $count = 1, $tickType = self::SUCCESS)
    {
        //Send it to the outputters
        $this->sendReportToOutputters('tick', $report);
    }

    // --------------------------------------------------------------    

    /**
     * Build a report and send it to the finish method in the outputters
     *
     * @param string $msg
     */
    public function finish($msg)
    {
        //Send report to finish method in outputter
        $report = $this->buildReport($msg);
        $this->sendReportToOutputters('finish', $report);
    }

    // --------------------------------------------------------------    

    /**
     * Build a report and send it to the abort method in the outputters
     *
     * @param string $msg
     */
    public function abort($msg)
    {
        $report = $this->buildReport($msg);
        $this->sendReportToOutputters('abort', $report);
    }

    // --------------------------------------------------------------    

    /**
     * Send a report to the outputters
     *
     * @param string $method  Which method to call on the outputters
     * @param Report $report  The report to send
     */
    private function sendReportToOutputters($method, Report $report)
    {
        array_map(function($obj) use ($report) {
            call_user_func(array($obj, $method), $report)
        }, $this->outputters);
    }

    // --------------------------------------------------------------    

    /** 
     * Build a report
     *
     * @param string $msg     An optional message for the report
     * @param int $increment  Number to increment by
     * @param int $incType    SUCCESS (default), WARN, or FAIL s
     */
    protected function buildReport($msg = null, $increment = 1, $incType = self::SUCCESS)
    {
        //Build a report and return it
    }
}

/* EOF: TaskTracker.php */