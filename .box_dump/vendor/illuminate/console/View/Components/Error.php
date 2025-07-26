<?php

namespace Illuminate\Console\View\Components;

use Symfony\Component\Console\Output\OutputInterface;

class Error extends Component
{







public function render($string, $verbosity = OutputInterface::VERBOSITY_NORMAL)
{
with(new Line($this->output))->render('error', $string, $verbosity);
}
}
