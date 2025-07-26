<?php

declare(strict_types=1);










namespace Carbon;

use Closure;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use InvalidArgumentException;
use ReflectionMethod;
use RuntimeException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;














































































































class Factory
{
protected string $className = Carbon::class;

protected array $settings = [];




protected Closure|CarbonInterface|null $testNow = null;




protected ?string $testDefaultTimezone = null;




protected bool $useTimezoneFromTestNow = false;




protected TranslatorInterface $translator;




protected array $weekendDays = [
CarbonInterface::SATURDAY,
CarbonInterface::SUNDAY,
];






protected array $regexFormats = [
'd' => '(3[01]|[12][0-9]|0[1-9])',
'D' => '(Sun|Mon|Tue|Wed|Thu|Fri|Sat)',
'j' => '([123][0-9]|[1-9])',
'l' => '([a-zA-Z]{2,})',
'N' => '([1-7])',
'S' => '(st|nd|rd|th)',
'w' => '([0-6])',
'z' => '(36[0-5]|3[0-5][0-9]|[12][0-9]{2}|[1-9]?[0-9])',
'W' => '(5[012]|[1-4][0-9]|0?[1-9])',
'F' => '([a-zA-Z]{2,})',
'm' => '(1[012]|0[1-9])',
'M' => '([a-zA-Z]{3})',
'n' => '(1[012]|[1-9])',
't' => '(2[89]|3[01])',
'L' => '(0|1)',
'o' => '([1-9][0-9]{0,4})',
'Y' => '([1-9]?[0-9]{4})',
'y' => '([0-9]{2})',
'a' => '(am|pm)',
'A' => '(AM|PM)',
'B' => '([0-9]{3})',
'g' => '(1[012]|[1-9])',
'G' => '(2[0-3]|1?[0-9])',
'h' => '(1[012]|0[1-9])',
'H' => '(2[0-3]|[01][0-9])',
'i' => '([0-5][0-9])',
's' => '([0-5][0-9])',
'u' => '([0-9]{1,6})',
'v' => '([0-9]{1,3})',
'e' => '([a-zA-Z]{1,5})|([a-zA-Z]*\\/[a-zA-Z]*)',
'I' => '(0|1)',
'O' => '([+-](1[0123]|0[0-9])[0134][05])',
'P' => '([+-](1[0123]|0[0-9]):[0134][05])',
'p' => '(Z|[+-](1[0123]|0[0-9]):[0134][05])',
'T' => '([a-zA-Z]{1,5})',
'Z' => '(-?[1-5]?[0-9]{1,4})',
'U' => '([0-9]*)',


'c' => '(([1-9]?[0-9]{4})-(1[012]|0[1-9])-(3[01]|[12][0-9]|0[1-9])T(2[0-3]|[01][0-9]):([0-5][0-9]):([0-5][0-9])[+-](1[012]|0[0-9]):([0134][05]))', 
'r' => '(([a-zA-Z]{3}), ([123][0-9]|0[1-9]) ([a-zA-Z]{3}) ([1-9]?[0-9]{4}) (2[0-3]|[01][0-9]):([0-5][0-9]):([0-5][0-9]) [+-](1[012]|0[0-9])([0134][05]))', 
];






protected array $regexFormatModifiers = [
'*' => '.+',
' ' => '[   ]',
'#' => '[;:\\/.,()-]',
'?' => '([^a]|[a])',
'!' => '',
'|' => '',
'+' => '',
];

public function __construct(array $settings = [], ?string $className = null)
{
if ($className) {
$this->className = $className;
}

$this->settings = $settings;
}

public function getClassName(): string
{
return $this->className;
}

public function setClassName(string $className): self
{
$this->className = $className;

return $this;
}

public function className(?string $className = null): self|string
{
return $className === null ? $this->getClassName() : $this->setClassName($className);
}

public function getSettings(): array
{
return $this->settings;
}

public function setSettings(array $settings): self
{
$this->settings = $settings;

return $this;
}

public function settings(?array $settings = null): self|array
{
return $settings === null ? $this->getSettings() : $this->setSettings($settings);
}

public function mergeSettings(array $settings): self
{
$this->settings = array_merge($this->settings, $settings);

return $this;
}

public function setHumanDiffOptions(int $humanDiffOptions): void
{
$this->mergeSettings([
'humanDiffOptions' => $humanDiffOptions,
]);
}

public function enableHumanDiffOption($humanDiffOption): void
{
$this->setHumanDiffOptions($this->getHumanDiffOptions() | $humanDiffOption);
}

public function disableHumanDiffOption(int $humanDiffOption): void
{
$this->setHumanDiffOptions($this->getHumanDiffOptions() & ~$humanDiffOption);
}

public function getHumanDiffOptions(): int
{
return (int) ($this->getSettings()['humanDiffOptions'] ?? 0);
}

/**
@param-closure-this
















*/
public function macro(string $name, ?callable $macro): void
{
$macros = $this->getSettings()['macros'] ?? [];
$macros[$name] = $macro;

$this->mergeSettings([
'macros' => $macros,
]);
}




public function resetMacros(): void
{
$this->mergeSettings([
'macros' => null,
'genericMacros' => null,
]);
}









public function genericMacro(callable $macro, int $priority = 0): void
{
$genericMacros = $this->getSettings()['genericMacros'] ?? [];

if (!isset($genericMacros[$priority])) {
$genericMacros[$priority] = [];
krsort($genericMacros, SORT_NUMERIC);
}

$genericMacros[$priority][] = $macro;

$this->mergeSettings([
'genericMacros' => $genericMacros,
]);
}




public function hasMacro(string $name): bool
{
return isset($this->getSettings()['macros'][$name]);
}




public function getMacro(string $name): ?callable
{
return $this->getSettings()['macros'][$name] ?? null;
}




public function setTranslator(TranslatorInterface $translator): void
{
$this->translator = $translator;
}




public function getTranslator(): TranslatorInterface
{
return $this->translator ??= Translator::get();
}






public function resetToStringFormat(): void
{
$this->setToStringFormat(null);
}




public function setToStringFormat(string|Closure|null $format): void
{
$this->mergeSettings([
'toStringFormat' => $format,
]);
}




public function serializeUsing(string|callable|null $format): void
{
$this->mergeSettings([
'toJsonFormat' => $format,
]);
}




public function useStrictMode(bool $strictModeEnabled = true): void
{
$this->mergeSettings([
'strictMode' => $strictModeEnabled,
]);
}





public function isStrictModeEnabled(): bool
{
return $this->getSettings()['strictMode'] ?? true;
}




public function useMonthsOverflow(bool $monthsOverflow = true): void
{
$this->mergeSettings([
'monthOverflow' => $monthsOverflow,
]);
}




public function resetMonthsOverflow(): void
{
$this->useMonthsOverflow();
}




public function shouldOverflowMonths(): bool
{
return $this->getSettings()['monthOverflow'] ?? true;
}




public function useYearsOverflow(bool $yearsOverflow = true): void
{
$this->mergeSettings([
'yearOverflow' => $yearsOverflow,
]);
}




public function resetYearsOverflow(): void
{
$this->useYearsOverflow();
}




public function shouldOverflowYears(): bool
{
return $this->getSettings()['yearOverflow'] ?? true;
}






public function getWeekendDays(): array
{
return $this->weekendDays;
}




public function setWeekendDays(array $days): void
{
$this->weekendDays = $days;
}










public function hasFormat(string $date, string $format): bool
{




return $this->matchFormatPattern($date, preg_quote($format, '/'), $this->regexFormats);
}










public function hasFormatWithModifiers(string $date, string $format): bool
{
return $this->matchFormatPattern($date, $format, array_merge($this->regexFormats, $this->regexFormatModifiers));
}























public function setTestNow(mixed $testNow = null): void
{
$this->useTimezoneFromTestNow = false;
$this->testNow = $testNow instanceof self || $testNow instanceof Closure
? $testNow
: $this->make($testNow);
}




















public function setTestNowAndTimezone(mixed $testNow = null, $timezone = null): void
{
if ($testNow) {
$this->testDefaultTimezone ??= date_default_timezone_get();
}

$useDateInstanceTimezone = $testNow instanceof DateTimeInterface;

if ($useDateInstanceTimezone) {
$this->setDefaultTimezone($testNow->getTimezone()->getName(), $testNow);
}

$this->setTestNow($testNow);
$this->useTimezoneFromTestNow = ($timezone === null && $testNow instanceof Closure);

if (!$useDateInstanceTimezone) {
$now = $this->getMockedTestNow(\func_num_args() === 1 ? null : $timezone);
$this->setDefaultTimezone($now?->tzName ?? $this->testDefaultTimezone ?? 'UTC', $now);
}

if (!$testNow) {
$this->testDefaultTimezone = null;
}
}

/**
@template











*/
public function withTestNow(mixed $testNow, callable $callback): mixed
{
$this->setTestNow($testNow);

try {
$result = $callback();
} finally {
$this->setTestNow();
}

return $result;
}







public function getTestNow(): Closure|CarbonInterface|null
{
if ($this->testNow === null) {
$factory = FactoryImmutable::getDefaultInstance();

if ($factory !== $this) {
return $factory->getTestNow();
}
}

return $this->testNow;
}

public function handleTestNowClosure(
Closure|CarbonInterface|null $testNow,
DateTimeZone|string|int|null $timezone = null,
): ?CarbonInterface {
if ($testNow instanceof Closure) {
$callback = Callback::fromClosure($testNow);
$realNow = new DateTimeImmutable('now');
$testNow = $testNow($callback->prepareParameter($this->parse(
$realNow->format('Y-m-d H:i:s.u'),
$timezone ?? $realNow->getTimezone(),
)));

if ($testNow !== null && !($testNow instanceof DateTimeInterface)) {
$function = $callback->getReflectionFunction();
$type = \is_object($testNow) ? $testNow::class : \gettype($testNow);

throw new RuntimeException(
'The test closure defined in '.$function->getFileName().
' at line '.$function->getStartLine().' returned '.$type.
'; expected '.CarbonInterface::class.'|null',
);
}

if (!($testNow instanceof CarbonInterface)) {
$timezone ??= $this->useTimezoneFromTestNow ? $testNow->getTimezone() : null;
$testNow = $this->__call('instance', [$testNow, $timezone]);
}
}

return $testNow;
}







public function hasTestNow(): bool
{
return $this->getTestNow() !== null;
}

public function withTimeZone(DateTimeZone|string|int|null $timezone): static
{
$factory = clone $this;
$factory->settings['timezone'] = $timezone;

return $factory;
}

public function __call(string $name, array $arguments): mixed
{
$method = new ReflectionMethod($this->className, $name);
$settings = $this->settings;

if ($settings && isset($settings['timezone'])) {
$timezoneParameters = array_filter($method->getParameters(), function ($parameter) {
return \in_array($parameter->getName(), ['tz', 'timezone'], true);
});
$timezoneSetting = $settings['timezone'];

if (isset($arguments[0]) && \in_array($name, ['instance', 'make', 'create', 'parse'], true)) {
if ($arguments[0] instanceof DateTimeInterface) {
$settings['innerTimezone'] = $settings['timezone'];
} elseif (\is_string($arguments[0]) && date_parse($arguments[0])['is_localtime']) {
unset($settings['timezone'], $settings['innerTimezone']);
}
}

if (\count($timezoneParameters)) {
$index = key($timezoneParameters);

if (!isset($arguments[$index])) {
array_splice($arguments, key($timezoneParameters), 0, [$timezoneSetting]);
}

unset($settings['timezone']);
}
}

$clock = FactoryImmutable::getCurrentClock();
FactoryImmutable::setCurrentClock($this);

try {
$result = $this->className::$name(...$arguments);
} finally {
FactoryImmutable::setCurrentClock($clock);
}

if (isset($this->translator)) {
$settings['translator'] = $this->translator;
}

return $result instanceof CarbonInterface && !empty($settings)
? $result->settings($settings)
: $result;
}




protected function getMockedTestNow(DateTimeZone|string|int|null $timezone): ?CarbonInterface
{
$testNow = $this->handleTestNowClosure($this->getTestNow());

if ($testNow instanceof CarbonInterface) {
$testNow = $testNow->avoidMutation();

if ($timezone !== null) {
return $testNow->setTimezone($timezone);
}
}

return $testNow;
}

















private function matchFormatPattern(string $date, string $format, array $replacements): bool
{

$regex = str_replace('\\\\', '\\', $format);

$regex = preg_replace_callback(
'/(?<!\\\\)((?:\\\\{2})*)(['.implode('', array_keys($replacements)).'])/',
static fn ($match) => $match[1].strtr($match[2], $replacements),
$regex,
);

$regex = preg_replace('/(?<!\\\\)((?:\\\\{2})*)\\\\(\w)/', '$1$2', $regex);

$regex = preg_replace('#(?<!\\\\)((?:\\\\{2})*)/#', '$1\\/', $regex);

return (bool) @preg_match('/^'.$regex.'$/', $date);
}

private function setDefaultTimezone(string $timezone, ?DateTimeInterface $date = null): void
{
$previous = null;
$success = false;

try {
$success = date_default_timezone_set($timezone);
} catch (Throwable $exception) {
$previous = $exception;
}

if (!$success) {
$suggestion = @CarbonTimeZone::create($timezone)->toRegionName($date);

throw new InvalidArgumentException(
"Timezone ID '$timezone' is invalid".
($suggestion && $suggestion !== $timezone ? ", did you mean '$suggestion'?" : '.')."\n".
"It must be one of the IDs from DateTimeZone::listIdentifiers(),\n".
'For the record, hours/minutes offset are relevant only for a particular moment, '.
'but not as a default timezone.',
0,
$previous
);
}
}
}
