<?php

namespace TaskTracker\OutputHandler;
use Symfony\Component\Console\Output\OutputInterface;
use TaskTracker\Report;

class SymfonyConsole extends OutputHandler
{
    /**
     * @var \Symfony\Component\Console\Output\OutputInterface;
     */
    private $output;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param Symfony\Component\Console\Output\OutputInterface
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    // --------------------------------------------------------------

    /** @inherit */
    public function tick(Report $report, $msg = null)
    {
        //If we have a finite task, build a status bar
        if ($report->totalItems > 0) {
            $firstLine = $this->buildStatusBar($msg, $report->numItems, $report->totalItems);
        }
        else { //or a spinner
            $firstLine = $this->buildSpinner($msg, $report->numItems);
        }

        //Build a report
        $secondLine = $this->formatReport($report);

        //If numTicks greater than 1, then backup a line
        //Alternatively, \033c clears the whole screen
        if ($report->numItems > 1) {
            $this->output->write("\033[F\033[F\033[F");
        }

        //Output (with clearn screen)
        $this->output->writeln($firstLine);
        $this->output->writeln($secondLine);
    }

    // --------------------------------------------------------------

    public function start(Report $initialReport, $msg = null)
    {
        $this->output->writeln(sprintf("\n\nStarting. . .\n%s", $msg));
    }

    // --------------------------------------------------------------

    /** @inherit */
    public function finish(Report $lastReport, $msg = null)
    {
        $this->output->writeln(sprintf("\n\nAll Done! %s\n\n", $msg));
    }

    // --------------------------------------------------------------

    /** @inherit */
    public function abort(Report $lastReport = null, $msg)
    {
        $this->output->writeln(sprintf("\n\nAborting. . . %s\n\n", $msg));
    }

    // --------------------------------------------------------------

    /**
     * Format a report
     *
     * xx | xx:xx | Mem: xx (max: xx) | Avg: xx (max: xx, min: xx)
     * xx processed | xx skipped | xx failed
     *
     * @param Report $report
     * @return string
     */
    protected function formatReport(Report $report)
    {
        //Variables
        $numItems    = number_format($report->numItems, 0);
        $timeElapsed = $this->formatTime($report->timeElapsed);
        $memoryUsage = number_format($report->memUsage / 1048576, 2); //mb
        $peakMemory  = number_format($report->peakMemory / 1048576, 2); //mb
        $average     = number_format($report->avgItemTime, 2);
        $maxTime     = number_format($report->maxItemTime, 2);
        $minTime     = number_format($report->minItemTime, 2);
        $numSuccess  = number_format($report->numItemsSuccess, 0);
        $numFailed   = number_format($report->numItemsFail, 0);
        $numSkipped  = number_format($report->numItemsSkip, 0);

        $lineOne = sprintf(
            "%s | %s | Mem: %smb (max: %smb) | Avg: %ss Max: %ss Min: %ss",
            $numItems, $timeElapsed, $memoryUsage, $peakMemory, $average,
            $maxTime, $minTime
        );
        $lineTwo = sprintf(
            "%s processed | %s skipped | %s failed",
            $numSuccess, $numSkipped, $numFailed
        );

        return $lineOne . "\n" . $lineTwo;
    }

    // --------------------------------------------------------------

    /** 
     * Build spinner
     *
     * @param string $msg
     * @param int $count
     * @return string
     */
    protected function buildSpinner($msg, $count)
    {
        $spinnerStates = array('.', '..', '...');

        if (empty($msg)) {
            $msg = "Processing";
        }

        $state = $count % count($spinnerStates);
        $state = $spinnerStates[$state];

        return sprintf("%s %s", trim($msg), $state);
    }

    // --------------------------------------------------------------

    /**
     * Build a status bar
     *
     * Deprecate when Symfony Console 1.2 comes out; it will be included
     *
     * @param string $msg
     * @param int $itemCount
     * @param int $totalItems  -1 for infinite
     * @return string
     */
    protected function buildStatusBar($msg, $itemCount, $totalItems)
    {
        //Determine newline and available width
        $availWidth = $this->getConsoleWidth() - strlen(trim($msg)) - 3;
        if ($availWidth <= 10) {
            $newLine = true;
            $availWidth = $this->getConsoleWidth() - 2;
        }
        else {
            $newLine = false;
        }

        //Percent as integer
        $percentInt = floor(($itemCount / $totalItems) * 100);

        //Minus the brackets [ ] and the percent width and the % sign 
        //and the arrow > symbol
        $lineArea   = $availWidth - 2 - strlen((string) $percentInt) - 1 - 1;
        $lineWidth  = floor(($itemCount * $lineArea) / $totalItems);

        //If more than 100%
        if ($lineWidth > $lineArea) {
            $lineWidth = $lineArea;
        }

        //Empty Width
        $emptyWidth = $lineArea - $lineWidth;

        //Build it
        $outStr  = trim($msg) . ' ';
        $outStr .= "[" . $percentInt . '%' . str_pad('', $lineWidth, '=') . '>';
        $outStr .= str_pad('', $emptyWidth, ' ') . ']';

        return $outStr;
    }

    // --------------------------------------------------------------

    /**
     * Attempt to get the console width
     *
     * @return int  Defaults to 80 if cannot detect
     */
    private function getConsoleWidth()
    {
        if (is_callable('exec') && strtoupper(substr(PHP_OS, 0, 3)) != 'WIN') {
            $result = (int) exec('/usr/bin/env tput cols');
        }
        else {
            $result = null;
        }

        return $result ?: 80;
    }

}

/* EOF: SymfonyConsole.php */