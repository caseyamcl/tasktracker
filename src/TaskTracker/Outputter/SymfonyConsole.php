<?php

namespace TaskTracker\Outputter;

class SymfonyConsole extends Outputter
{
    /**
     * @var [SYMFONY Conosle OUTPUT Class]
     */
    private $output

    public function __construct($output)
    {
        $this->output = $output;
    }

    // --------------------------------------------------------------

}

/* EOF: SymfonyConsole.php */