<?php

namespace TaskTracker\TestFixtures;
use TaskTracker\Report;

class FakeReportInfinite extends Report
{
    private $startTime       = 1350953953.0975;
    private $totalItems      = -1;
    private $numItems        = 2;
    private $timeElapsed     = 0.014626026153564;
    private $peakMemory      = 7340032;
    private $numItemsSuccess = 1;
    private $numItemsFail    = 0;
    private $numItemsSkip    = 1;
    private $itemTime        = 0.010622978210449;
    private $maxItemTime     = 0.010622978210449;
    private $minItemTime     = 0.0040030479431152;
    private $avgItemTime     = 0.0073130130767822;
    private $message         = 'Two';
    private $memUsage        = 7340032;
    private $timestamp       = 1350953953.1121;
    private $status          = -1;

    public function toArray()
    {
        return get_object_vars($this);
    }
}

/* EOF: FakeReportInfinite.php */