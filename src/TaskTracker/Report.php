<?php

namespace TaskTracker;

/**
 * Value object - represents a report
 */
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
     * @var int  The number of total items (-1 for infinity)
     */
    public $totalItems;
} 

/* EOF: Report.php */