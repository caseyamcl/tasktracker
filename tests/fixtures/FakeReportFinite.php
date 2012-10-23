<?php

namespace TaskTracker\TestFixtures;
use TaskTracker\Report;

class FakeReportFinite extends Report
{
    private $startTime       = 1350953978.2356;
    private $totalItems      = 20;
    private $numItems        = 2;
    private $timeElapsed     = 0.002849817276001;
    private $peakMemory      = 7340032;
    private $numItemsSuccess = 1;
    private $numItemsFail    = 0;
    private $numItemsSkip    = 1;
    private $itemTime        = 0.0012638568878174;
    private $maxItemTime     = 0.0015859603881836;
    private $minItemTime     = 0.0012638568878174;
    private $avgItemTime     = 0.0014249086380005;
    private $message         = 'Two';
    private $memUsage        = 7340032;
    private $timestamp       = 1350953978.2385;
    private $status          = -1;

    public function toArray()
    {
        return get_object_vars($this);
    }
}

/* EOF: FakeReportFinite.php */