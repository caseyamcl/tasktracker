<?php

namespace TaskTracker;

class Report
{
    const INFINITE = -1;

    // --------------------------------------------------------------

    //Basic Values
    
    /**
     * @var float  Start timestamp in microseconds
     */
    private $startTime;

    /**
     * @var int  Number of total items (-1 for infinite)
     */
    private $totalItems;

    /**
     * @var Tick  The current tick
     */
    private $tick;

    //Calculated Values

    /**
     * @var int  Number of items processed
     */
    private $numItems = 0;

    /**
     * @var float  The total time elaspsed
     */
    private $timeElapsed = 0;

    /**
     * @var int  The peak memory used (in bytes)
     */
    private $peakMemory  = 0;

    /**
     * @var int  Total number of items processed succesfully
     */
    private $numItemsSuccess = 0;

    /**
     * @var int  Total number of items processed but failed
     */
    private $numItemsFail = 0;

    /**
     * @var int  Total number of items skipped
     */
    private $numItemsSkip = 0;

    /**
     * @var float  The time elapsed since last tick
     */
    private $itemTime = 0;

    /**
     * @var float  Maximum item process time (in microseconds)
     */
    private $maxItemTime = 0;

    /**
     * @var float  Minimum item process time (in microseconds)
     */
    private $minItemTime = 0;

    /**
     * @var float  Average item process time (in microseconds)
     */
    private $avgItemTime = 0;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param int   $totalItems  -1 for infinite
     * @param float $startTime   If null, current time used
     */
    public function __construct($totalItems = self::INFINITE, $startTime = null)
    {
        //Set total items
        $this->totalItems = $totalItems;

        //Set start time
        $this->startTime = (float) $startTime ?: microtime(true);

        //No tick to start
        $this->tick = null;

        //Current memory for peak memory
        $this->peakMemory = memory_get_usage(true);
    }

    // --------------------------------------------------------------

    /**
     * GET Magic Method
     *
     * @param string $item
     * @return mixed
     */
    public function __get($item)
    {
        $arr = $this->toArray();
        return (isset($arr[$item])) ? $arr[$item] : null;
    }

    // --------------------------------------------------------------

    /**
     * To Array
     *
     * @return array
     */
    public function toArray()
    {
        if ($this->tick instanceOf Tick) {
            $arr = array_merge(get_object_vars($this), $this->tick->toArray());
            unset($arr['tick']);
            return $arr;
        } else {
            return get_object_vars($this);            
        }
    }

    // --------------------------------------------------------------

    /**
     * Tick
     *
     * @param Tick $tick
     */
    public function tick(Tick $tick)
    {
        //Get the prior tick
        $priorTick = $this->tick;

        //Set the new tick
        $this->tick = $tick;

        //Calculate some things

        //Number of items
        $this->numItems++;

        //Peak memory
        $this->peakMemory = memory_get_peak_usage(true);

        //Total time elapsed
        $this->timeElapsed = $this->tick->timestamp - $this->startTime;

        //Time elapsed since last tick
        $this->itemTime = ($priorTick)
            ? $this->tick->timestamp - $priorTick->timestamp
            : $this->timeElapsed;

        //Total number of items of a certain time
        switch($this->tick->status) {
            case Tick::SUCCESS: $this->numItemsSuccess++; break;
            case Tick::FAIL:    $this->numItemsFail++;    break;
            case Tick::SKIP:    $this->numItemsSkip++;    break;
        }

        //Max and min
        $this->maxItemTime = max(array($this->maxItemTime, $this->itemTime));
        $this->minItemTime = ($this->minItemTime)
            ? min(array($this->minItemTime, $this->itemTime))
            : $this->timeElapsed;

        //Average
        $this->avgItemTime = $this->timeElapsed / $this->numItems;
    }
}

/* EOF: Report.php */