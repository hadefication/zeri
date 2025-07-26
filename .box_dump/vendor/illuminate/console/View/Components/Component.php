<?php

namespace Illuminate\Console\View\Components;

use Illuminate\Console\OutputStyle;
use Illuminate\Console\QuestionHelper;
use ReflectionClass;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;

use function Termwind\render;
use function Termwind\renderUsing;

abstract class Component
{





protected $output;






protected $mutators;






public function __construct($output)
{
$this->output = $output;
}









protected function renderView($view, $data, $verbosity)
{
renderUsing($this->output);

render((string) $this->compile($view, $data), $verbosity);
}








protected function compile($view, $data)
{
extract($data);

ob_start();

include __DIR__."/../../resources/views/components/$view.php";

return tap(ob_get_contents(), function () {
ob_end_clean();
});
}








protected function mutate($data, $mutators)
{
foreach ($mutators as $mutator) {
$mutator = new $mutator;

if (is_iterable($data)) {
foreach ($data as $key => $value) {
$data[$key] = $mutator($value);
}
} else {
$data = $mutator($data);
}
}

return $data;
}







protected function usingQuestionHelper($callable)
{
$property = with(new ReflectionClass(OutputStyle::class))
->getParentClass()
->getProperty('questionHelper');

$currentHelper = $property->isInitialized($this->output)
? $property->getValue($this->output)
: new SymfonyQuestionHelper();

$property->setValue($this->output, new QuestionHelper);

try {
return $callable();
} finally {
$property->setValue($this->output, $currentHelper);
}
}
}
