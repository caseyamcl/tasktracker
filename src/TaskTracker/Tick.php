<?php

namespace TaskTracker;
use InvalidArgumentException;

/**
 * Represents all of the information associated with a single tick
 */
class Tick
{
    const SUCCESS  = 1;
    const FAIL     = 0;
    const SKIP     = -1;
    
    /**
     * @var string  Custom Message
     */
    private $message;

    /**
     * @var int  Memory Usage (in bytes) - real amount
     */
    private $memUsage;

    /**
     * @var float  Timestamp (microtime float)
     */
    private $timestamp;

    /**
     * @var int  The status of tick (success, fail, or skip)
     */
    private $status;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param string $message
     * @param int $action
     * @param int $status
     */
    public function __construct($message = null, $status = self::SUCCESS)
    {   
        if ($status < -1 OR $status > 1) {
            throw new InvalidArgumentException("Invalid tick status");
        }

        //Set parameters
        $this->status    = $status;
        $this->message   = $message;
        $this->memUsage  = memory_get_usage(true);
        $this->timestamp = microtime(true);
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
        return $this->$item;
    }

    // --------------------------------------------------------------

    /**
     * To Array
     *
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);        
    }

} 

/* EOF: Tick.php */