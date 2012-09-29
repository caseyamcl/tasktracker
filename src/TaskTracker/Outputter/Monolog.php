<?php

namespace TaskTracker\Outputter;

/**
 * The Monolog outputter outputs to Monolog
 */
class Monolog extends Outputter
{
    /**
     * @var \Monolog\[MAINCLASSNAME]
     */
    private $monolog;

    public function __construct(\Monolog)
    {

    }
}

/* EOF: Monolog.php */