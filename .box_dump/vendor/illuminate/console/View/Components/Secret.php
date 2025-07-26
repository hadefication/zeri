<?php

namespace Illuminate\Console\View\Components;

use Symfony\Component\Console\Question\Question;

class Secret extends Component
{







public function render($question, $fallback = true)
{
$question = new Question($question);

$question->setHidden(true)->setHiddenFallback($fallback);

return $this->usingQuestionHelper(fn () => $this->output->askQuestion($question));
}
}
