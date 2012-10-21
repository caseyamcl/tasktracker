<?php

namespace TaskTracker\OutputHandler;
use TaskTracker\Report;

abstract class OutputHandler
{
    /**
     * Start processing the task
     *
     * @param TaskTracker\Report $startReport
     * @param string $message
     */
    public abstract function start(Report $startReport, $message);

    /**
     * Provide a progress report
     *
     * @param TaskTracker\Report $report
     * @param string $message
     * @return void
     */
    public abstract function tick(Report $report, $message);

    /**
     * Finish the task
     *
     * @param TaskTracker\Report $lastReport
     * @param string $message
     * @return void
     */
    public abstract function finish(Report $lastReport, $message);

    /**
     * Abort the task prematurely
     *
     * @param TaskTracker\Report $lastReport
     * @param string $message
     * @return void
     */
    public abstract function abort(Report $lastReport, $message);

    //---------------------------------------------------------------

    /**
     * Returns human-readable time format
     *
     * @param float|int $eplapsedTime
     * @return string 
     */
    public function formatTime($elapsedTime)
    {
        $seconds = floor($elapsedTime);
        $output = array();

        //Hours (only if $seconds > 3600)
        if ($seconds > 3600) {
            $hours    = floor($seconds / 3600);
            $seconds  = $seconds - (3600 * $hours);
            $output[] = $hours;
        }

        //Minutes
        if ($seconds >= 60) {
            $minutes  = floor($seconds / 60);
            $seconds  = $seconds - ($minutes * 60);
            $output[] = str_pad((string) $minutes, 2, '0', STR_PAD_LEFT);
        }
        else {
            $output[] = '00';
        }

        //Seconds
        if ($seconds > 0) {
            $output[] = str_pad((string) $seconds, 2, '0', STR_PAD_LEFT);
        }
        else {
            $output[] = '00';
        }

        return implode(":", $output);
    }
}

/* EOF: Outputter.php */