<?php

namespace Illuminate\Console\View\Components;

class Confirm extends Component
{







public function render($question, $default = false)
{
return $this->usingQuestionHelper(
fn () => $this->output->confirm($question, $default),
);
}
}
