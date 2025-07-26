<?php

declare(strict_types=1);










namespace Carbon\Traits;

use Carbon\Exceptions\InvalidFormatException;
use Carbon\FactoryImmutable;
use DateTimeZone;
use ReturnTypeWillChange;
use Throwable;


















trait Serialization
{
use ObjectInitialisation;






protected array $dumpProperties = ['date', 'timezone_type', 'timezone'];






protected $dumpLocale;







protected $dumpDateProperties;




public function serialize(): string
{
return serialize($this);
}



















public static function fromSerialized($value, array $options = []): static
{
$instance = @unserialize((string) $value, $options);

if (!$instance instanceof static) {
throw new InvalidFormatException("Invalid serialized value: $value");
}

return $instance;
}








#[ReturnTypeWillChange]
public static function __set_state($dump): static
{
if (\is_string($dump)) {
return static::parse($dump);
}


$date = get_parent_class(static::class) && method_exists(parent::class, '__set_state')
? parent::__set_state((array) $dump)
: (object) $dump;

return static::instance($date);
}








public function __sleep()
{
$properties = $this->getSleepProperties();

if ($this->localTranslator ?? null) {
$properties[] = 'dumpLocale';
$this->dumpLocale = $this->locale ?? null;
}

return $properties;
}








public function __serialize(): array
{

if (isset($this->timezone_type, $this->timezone, $this->date)) {
return [
'date' => $this->date,
'timezone_type' => $this->timezone_type,
'timezone' => $this->dumpTimezone($this->timezone),
];
}


$timezone = $this->getTimezone();
$export = [
'date' => $this->format('Y-m-d H:i:s.u'),
'timezone_type' => $timezone->getType(),
'timezone' => $timezone->getName(),
];


if (\extension_loaded('msgpack') && isset($this->constructedObjectId)) {
$timezone = $this->timezone ?? null;
$export['dumpDateProperties'] = [
'date' => $this->format('Y-m-d H:i:s.u'),
'timezone' => $this->dumpTimezone($timezone),
];
}


if ($this->localTranslator ?? null) {
$export['dumpLocale'] = $this->locale ?? null;
}

return $export;
}






public function __wakeup(): void
{
if (parent::class && method_exists(parent::class, '__wakeup')) {

try {
parent::__wakeup();
} catch (Throwable $exception) {
try {

['date' => $date, 'timezone' => $timezone] = $this->dumpDateProperties;
parent::__construct($date, $timezone);
} catch (Throwable) {
throw $exception;
}
}

}

$this->constructedObjectId = spl_object_hash($this);

if (isset($this->dumpLocale)) {
$this->locale($this->dumpLocale);
$this->dumpLocale = null;
}

$this->cleanupDumpProperties();
}






public function __unserialize(array $data): void
{

try {
$this->__construct($data['date'] ?? null, $data['timezone'] ?? null);
} catch (Throwable $exception) {
if (!isset($data['dumpDateProperties']['date'], $data['dumpDateProperties']['timezone'])) {
throw $exception;
}

try {

['date' => $date, 'timezone' => $timezone] = $data['dumpDateProperties'];
$this->__construct($date, $timezone);
} catch (Throwable) {
throw $exception;
}
}


if (isset($data['dumpLocale'])) {
$this->locale($data['dumpLocale']);
}
}




public function jsonSerialize(): mixed
{
$serializer = $this->localSerializer
?? $this->getFactory()->getSettings()['toJsonFormat']
?? null;

if ($serializer) {
return \is_string($serializer)
? $this->rawFormat($serializer)
: $serializer($this);
}

return $this->toJSON();
}







public static function serializeUsing(string|callable|null $format): void
{
FactoryImmutable::getDefaultInstance()->serializeUsing($format);
}








public function cleanupDumpProperties(): self
{

if (PHP_VERSION < 8.2) {
foreach ($this->dumpProperties as $property) {
if (isset($this->$property)) {
unset($this->$property);
}
}
}


return $this;
}

private function getSleepProperties(): array
{
$properties = $this->dumpProperties;


if (!\extension_loaded('msgpack')) {
return $properties;
}

if (isset($this->constructedObjectId)) {
$timezone = $this->timezone ?? null;
$this->dumpDateProperties = [
'date' => $this->format('Y-m-d H:i:s.u'),
'timezone' => $this->dumpTimezone($timezone),
];

$properties[] = 'dumpDateProperties';
}

return $properties;

}


private function dumpTimezone(mixed $timezone): mixed
{
return $timezone instanceof DateTimeZone ? $timezone->getName() : $timezone;
}
}
