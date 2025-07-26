<?php

namespace Illuminate\Console\View\Components;

use Symfony\Component\Console\Question\ChoiceQuestion;

class Choice extends Component
{










public function render($question, $choices, $default = null, $attempts = null, $multiple = false)
{
return $this->usingQuestionHelper(
fn () => $this->output->askQuestion(
$this->getChoiceQuestion($question, $choices, $default)
->setMaxAttempts($attempts)
->setMultiselect($multiple)
),
);
}









protected function getChoiceQuestion($question, $choices, $default)
{
return new class($question, $choices, $default) extends ChoiceQuestion
{
protected function isAssoc(array $array): bool
{
return ! array_is_list($array);
}
};
}
}
