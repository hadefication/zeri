<?php

namespace Illuminate\Console\Concerns;

use Closure;
use Illuminate\Console\OutputStyle;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

trait InteractsWithIO
{





protected $components;






protected $input;






protected $output;






protected $verbosity = OutputInterface::VERBOSITY_NORMAL;






protected $verbosityMap = [
'v' => OutputInterface::VERBOSITY_VERBOSE,
'vv' => OutputInterface::VERBOSITY_VERY_VERBOSE,
'vvv' => OutputInterface::VERBOSITY_DEBUG,
'quiet' => OutputInterface::VERBOSITY_QUIET,
'normal' => OutputInterface::VERBOSITY_NORMAL,
];







public function hasArgument($name)
{
return $this->input->hasArgument($name);
}







public function argument($key = null)
{
if (is_null($key)) {
return $this->input->getArguments();
}

return $this->input->getArgument($key);
}






public function arguments()
{
return $this->argument();
}







public function hasOption($name)
{
return $this->input->hasOption($name);
}







public function option($key = null)
{
if (is_null($key)) {
return $this->input->getOptions();
}

return $this->input->getOption($key);
}






public function options()
{
return $this->option();
}








public function confirm($question, $default = false)
{
return $this->output->confirm($question, $default);
}








public function ask($question, $default = null)
{
return $this->output->ask($question, $default);
}









public function anticipate($question, $choices, $default = null)
{
return $this->askWithCompletion($question, $choices, $default);
}









public function askWithCompletion($question, $choices, $default = null)
{
$question = new Question($question, $default);

is_callable($choices)
? $question->setAutocompleterCallback($choices)
: $question->setAutocompleterValues($choices);

return $this->output->askQuestion($question);
}








public function secret($question, $fallback = true)
{
$question = new Question($question);

$question->setHidden(true)->setHiddenFallback($fallback);

return $this->output->askQuestion($question);
}











public function choice($question, array $choices, $default = null, $attempts = null, $multiple = false)
{
$question = new ChoiceQuestion($question, $choices, $default);

$question->setMaxAttempts($attempts)->setMultiselect($multiple);

return $this->output->askQuestion($question);
}










public function table($headers, $rows, $tableStyle = 'default', array $columnStyles = [])
{
$table = new Table($this->output);

if ($rows instanceof Arrayable) {
$rows = $rows->toArray();
}

$table->setHeaders((array) $headers)->setRows($rows)->setStyle($tableStyle);

foreach ($columnStyles as $columnIndex => $columnStyle) {
$table->setColumnStyle($columnIndex, $columnStyle);
}

$table->render();
}








public function withProgressBar($totalSteps, Closure $callback)
{
$bar = $this->output->createProgressBar(
is_iterable($totalSteps) ? count($totalSteps) : $totalSteps
);

$bar->start();

if (is_iterable($totalSteps)) {
foreach ($totalSteps as $key => $value) {
$callback($value, $bar, $key);

$bar->advance();
}
} else {
$callback($bar);
}

$bar->finish();

if (is_iterable($totalSteps)) {
return $totalSteps;
}
}








public function info($string, $verbosity = null)
{
$this->line($string, 'info', $verbosity);
}









public function line($string, $style = null, $verbosity = null)
{
$styled = $style ? "<$style>$string</$style>" : $string;

$this->output->writeln($styled, $this->parseVerbosity($verbosity));
}








public function comment($string, $verbosity = null)
{
$this->line($string, 'comment', $verbosity);
}








public function question($string, $verbosity = null)
{
$this->line($string, 'question', $verbosity);
}








public function error($string, $verbosity = null)
{
$this->line($string, 'error', $verbosity);
}








public function warn($string, $verbosity = null)
{
if (! $this->output->getFormatter()->hasStyle('warning')) {
$style = new OutputFormatterStyle('yellow');

$this->output->getFormatter()->setStyle('warning', $style);
}

$this->line($string, 'warning', $verbosity);
}








public function alert($string, $verbosity = null)
{
$length = Str::length(strip_tags($string)) + 12;

$this->comment(str_repeat('*', $length), $verbosity);
$this->comment('*     '.$string.'     *', $verbosity);
$this->comment(str_repeat('*', $length), $verbosity);

$this->comment('', $verbosity);
}







public function newLine($count = 1)
{
$this->output->newLine($count);

return $this;
}







public function setInput(InputInterface $input)
{
$this->input = $input;
}







public function setOutput(OutputStyle $output)
{
$this->output = $output;
}







protected function setVerbosity($level)
{
$this->verbosity = $this->parseVerbosity($level);
}







protected function parseVerbosity($level = null)
{
if (isset($this->verbosityMap[$level])) {
$level = $this->verbosityMap[$level];
} elseif (! is_int($level)) {
$level = $this->verbosity;
}

return $level;
}






public function getOutput()
{
return $this->output;
}






public function outputComponents()
{
return $this->components;
}
}
