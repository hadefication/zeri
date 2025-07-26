<?php

declare(strict_types=1);










namespace Carbon\Traits;

use Carbon\CarbonInterface;
use DateTimeInterface;
use Throwable;










trait Options
{
use StaticOptions;
use Localization;





protected ?bool $localMonthsOverflow = null;





protected ?bool $localYearsOverflow = null;





protected ?bool $localStrictModeEnabled = null;




protected ?int $localHumanDiffOptions = null;






protected $localToStringFormat = null;






protected $localSerializer = null;




protected ?array $localMacros = null;




protected ?array $localGenericMacros = null;






protected $localFormatFunction = null;


















public function settings(array $settings): static
{
$this->localStrictModeEnabled = $settings['strictMode'] ?? null;
$this->localMonthsOverflow = $settings['monthOverflow'] ?? null;
$this->localYearsOverflow = $settings['yearOverflow'] ?? null;
$this->localHumanDiffOptions = $settings['humanDiffOptions'] ?? null;
$this->localToStringFormat = $settings['toStringFormat'] ?? null;
$this->localSerializer = $settings['toJsonFormat'] ?? null;
$this->localMacros = $settings['macros'] ?? null;
$this->localGenericMacros = $settings['genericMacros'] ?? null;
$this->localFormatFunction = $settings['formatFunction'] ?? null;

if (isset($settings['locale'])) {
$locales = $settings['locale'];

if (!\is_array($locales)) {
$locales = [$locales];
}

$this->locale(...$locales);
} elseif (isset($settings['translator']) && property_exists($this, 'localTranslator')) {
$this->localTranslator = $settings['translator'];
}

if (isset($settings['innerTimezone'])) {
return $this->setTimezone($settings['innerTimezone']);
}

if (isset($settings['timezone'])) {
return $this->shiftTimezone($settings['timezone']);
}

return $this;
}




public function getSettings(): array
{
$settings = [];
$map = [
'localStrictModeEnabled' => 'strictMode',
'localMonthsOverflow' => 'monthOverflow',
'localYearsOverflow' => 'yearOverflow',
'localHumanDiffOptions' => 'humanDiffOptions',
'localToStringFormat' => 'toStringFormat',
'localSerializer' => 'toJsonFormat',
'localMacros' => 'macros',
'localGenericMacros' => 'genericMacros',
'locale' => 'locale',
'tzName' => 'timezone',
'localFormatFunction' => 'formatFunction',
];

foreach ($map as $property => $key) {
$value = $this->$property ?? null;

if ($value !== null && ($key !== 'locale' || $value !== 'en' || $this->localTranslator)) {
$settings[$key] = $value;
}
}

return $settings;
}




public function __debugInfo(): array
{
$infos = array_filter(get_object_vars($this), static function ($var) {
return $var;
});

foreach (['dumpProperties', 'constructedObjectId', 'constructed', 'originalInput'] as $property) {
if (isset($infos[$property])) {
unset($infos[$property]);
}
}

$this->addExtraDebugInfos($infos);

foreach (["\0*\0", ''] as $prefix) {
$key = $prefix.'carbonRecurrences';

if (\array_key_exists($key, $infos)) {
$infos['recurrences'] = $infos[$key];
unset($infos[$key]);
}
}

return $infos;
}

protected function isLocalStrictModeEnabled(): bool
{
return $this->localStrictModeEnabled
?? $this->transmitFactory(static fn () => static::isStrictModeEnabled());
}

protected function addExtraDebugInfos(array &$infos): void
{
if ($this instanceof DateTimeInterface) {
try {
$infos['date'] ??= $this->format(CarbonInterface::MOCK_DATETIME_FORMAT);
$infos['timezone'] ??= $this->tzName ?? $this->timezoneSetting ?? $this->timezone ?? null;
} catch (Throwable) {

}
}
}
}
