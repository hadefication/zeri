<?php










namespace Symfony\Component\Console\Helper;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Output\OutputInterface;




class ProgressIndicator
{
private const FORMATS = [
'normal' => ' %indicator% %message%',
'normal_no_ansi' => ' %message%',

'verbose' => ' %indicator% %message% (%elapsed:6s%)',
'verbose_no_ansi' => ' %message% (%elapsed:6s%)',

'very_verbose' => ' %indicator% %message% (%elapsed:6s%, %memory:6s%)',
'very_verbose_no_ansi' => ' %message% (%elapsed:6s%, %memory:6s%)',
];

private int $startTime;
private ?string $format = null;
private ?string $message = null;
private array $indicatorValues;
private int $indicatorCurrent;
private string $finishedIndicatorValue;
private float $indicatorUpdateTime;
private bool $started = false;
private bool $finished = false;




private static array $formatters;





public function __construct(
private OutputInterface $output,
?string $format = null,
private int $indicatorChangeInterval = 100,
?array $indicatorValues = null,
?string $finishedIndicatorValue = null,
) {
$format ??= $this->determineBestFormat();
$indicatorValues ??= ['-', '\\', '|', '/'];
$indicatorValues = array_values($indicatorValues);
$finishedIndicatorValue ??= '✔';

if (2 > \count($indicatorValues)) {
throw new InvalidArgumentException('Must have at least 2 indicator value characters.');
}

$this->format = self::getFormatDefinition($format);
$this->indicatorValues = $indicatorValues;
$this->finishedIndicatorValue = $finishedIndicatorValue;
$this->startTime = time();
}




public function setMessage(?string $message): void
{
$this->message = $message;

$this->display();
}




public function start(string $message): void
{
if ($this->started) {
throw new LogicException('Progress indicator already started.');
}

$this->message = $message;
$this->started = true;
$this->finished = false;
$this->startTime = time();
$this->indicatorUpdateTime = $this->getCurrentTimeInMilliseconds() + $this->indicatorChangeInterval;
$this->indicatorCurrent = 0;

$this->display();
}




public function advance(): void
{
if (!$this->started) {
throw new LogicException('Progress indicator has not yet been started.');
}

if (!$this->output->isDecorated()) {
return;
}

$currentTime = $this->getCurrentTimeInMilliseconds();

if ($currentTime < $this->indicatorUpdateTime) {
return;
}

$this->indicatorUpdateTime = $currentTime + $this->indicatorChangeInterval;
++$this->indicatorCurrent;

$this->display();
}






public function finish(string $message): void
{
$finishedIndicator = 1 < \func_num_args() ? func_get_arg(1) : null;
if (null !== $finishedIndicator && !\is_string($finishedIndicator)) {
throw new \TypeError(\sprintf('Argument 2 passed to "%s()" must be of the type string or null, "%s" given.', __METHOD__, get_debug_type($finishedIndicator)));
}

if (!$this->started) {
throw new LogicException('Progress indicator has not yet been started.');
}

if (null !== $finishedIndicator) {
$this->finishedIndicatorValue = $finishedIndicator;
}

$this->finished = true;
$this->message = $message;
$this->display();
$this->output->writeln('');
$this->started = false;
}




public static function getFormatDefinition(string $name): ?string
{
return self::FORMATS[$name] ?? null;
}






public static function setPlaceholderFormatterDefinition(string $name, callable $callable): void
{
self::$formatters ??= self::initPlaceholderFormatters();

self::$formatters[$name] = $callable;
}




public static function getPlaceholderFormatterDefinition(string $name): ?callable
{
self::$formatters ??= self::initPlaceholderFormatters();

return self::$formatters[$name] ?? null;
}

private function display(): void
{
if (OutputInterface::VERBOSITY_QUIET === $this->output->getVerbosity()) {
return;
}

$this->overwrite(preg_replace_callback('{%([a-z\-_]+)(?:\:([^%]+))?%}i', function ($matches) {
if ($formatter = self::getPlaceholderFormatterDefinition($matches[1])) {
return $formatter($this);
}

return $matches[0];
}, $this->format ?? ''));
}

private function determineBestFormat(): string
{
return match ($this->output->getVerbosity()) {

OutputInterface::VERBOSITY_VERBOSE => $this->output->isDecorated() ? 'verbose' : 'verbose_no_ansi',
OutputInterface::VERBOSITY_VERY_VERBOSE,
OutputInterface::VERBOSITY_DEBUG => $this->output->isDecorated() ? 'very_verbose' : 'very_verbose_no_ansi',
default => $this->output->isDecorated() ? 'normal' : 'normal_no_ansi',
};
}




private function overwrite(string $message): void
{
if ($this->output->isDecorated()) {
$this->output->write("\x0D\x1B[2K");
$this->output->write($message);
} else {
$this->output->writeln($message);
}
}

private function getCurrentTimeInMilliseconds(): float
{
return round(microtime(true) * 1000);
}




private static function initPlaceholderFormatters(): array
{
return [
'indicator' => fn (self $indicator) => $indicator->finished ? $indicator->finishedIndicatorValue : $indicator->indicatorValues[$indicator->indicatorCurrent % \count($indicator->indicatorValues)],
'message' => fn (self $indicator) => $indicator->message,
'elapsed' => fn (self $indicator) => Helper::formatTime(time() - $indicator->startTime, 2),
'memory' => fn () => Helper::formatMemory(memory_get_usage(true)),
];
}
}
