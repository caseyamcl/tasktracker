<?php

namespace TaskTracker\TestFixtures;
use TaskTracker\Report;

/**
 * Fake Report (Infinite)
 */
class FakeReportInfinite extends Report
{
    public $currMessage   = "Test Message";
    public $currMemUsage  = 3144728;
    public $maxMemUsage   = 4234021;
    public $currentTime   = 1349894944.2343;
    public $timeTotal     = 3612;
    public $timeSinceLastTick = 3.45;
    public $numTicks        = 13;
    public $numItems        = 25;
    public $numItemsSuccess = 15;
    public $numItemsWarn    = 4;
    public $numItemsFail    = 3;
    public $numItemsSkip    = 3;
    public $avgTickTime     = 2.15;
    public $maxTickTime     = 3.04;
    public $minTickTime     = 0.3;
    public $medianTickTime  = 2.74;
    public $totalItems      = -1;
    public $startTime       = 1349894940.7645;
    public $action          = 'tick';
}

/* EOF: FakeReport.php */