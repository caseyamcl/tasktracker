  <?php

namespace TaskTracker;

/**
 * Value object - represents a report
 */
class Report
{
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
     * @var int  Number of seconds
     */
    public $timeTotal;

    /**
     * @var int  Number of seconds
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
     * @var int  Total number of succeeded items
     */
    public $numItemsSuccess;

    /**
     * @var int  Total number of warn items
     */
    public $numItemsWarn;

    /**
     * @var int  Total number of error items
     */
    public $numItemsError;

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
} 

/* EOF: Report.php */