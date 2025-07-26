<?php

declare(strict_types=1);










namespace Carbon;

use JsonSerializable;

class Language implements JsonSerializable
{
protected static ?array $languagesNames = null;

protected static ?array $regionsNames = null;

protected string $id;

protected string $code;

protected ?string $variant = null;

protected ?string $region = null;

protected ?array $names = null;

protected ?string $isoName = null;

protected ?string $nativeName = null;

public function __construct(string $id)
{
$this->id = str_replace('-', '_', $id);
$parts = explode('_', $this->id);
$this->code = $parts[0];

if (isset($parts[1])) {
if (!preg_match('/^[A-Z]+$/', $parts[1])) {
$this->variant = $parts[1];
$parts[1] = $parts[2] ?? null;
}
if ($parts[1]) {
$this->region = $parts[1];
}
}
}






public static function all(): array
{
static::$languagesNames ??= require __DIR__.'/List/languages.php';

return static::$languagesNames;
}







public static function regions(): array
{
static::$regionsNames ??= require __DIR__.'/List/regions.php';

return static::$regionsNames;
}




public function getNames(): array
{
$this->names ??= static::all()[$this->code] ?? [
'isoName' => $this->code,
'nativeName' => $this->code,
];

return $this->names;
}




public function getId(): string
{
return $this->id;
}




public function getCode(): string
{
return $this->code;
}




public function getVariant(): ?string
{
return $this->variant;
}




public function getVariantName(): ?string
{
if ($this->variant === 'Latn') {
return 'Latin';
}

if ($this->variant === 'Cyrl') {
return 'Cyrillic';
}

return $this->variant;
}




public function getRegion(): ?string
{
return $this->region;
}







public function getRegionName(): ?string
{
return $this->region ? (static::regions()[$this->region] ?? $this->region) : null;
}




public function getFullIsoName(): string
{
$this->isoName ??= $this->getNames()['isoName'];

return $this->isoName;
}




public function setIsoName(string $isoName): static
{
$this->isoName = $isoName;

return $this;
}




public function getFullNativeName(): string
{
$this->nativeName ??= $this->getNames()['nativeName'];

return $this->nativeName;
}




public function setNativeName(string $nativeName): static
{
$this->nativeName = $nativeName;

return $this;
}




public function getIsoName(): string
{
$name = $this->getFullIsoName();

return trim(strstr($name, ',', true) ?: $name);
}




public function getNativeName(): string
{
$name = $this->getFullNativeName();

return trim(strstr($name, ',', true) ?: $name);
}




public function getIsoDescription(): string
{
$region = $this->getRegionName();
$variant = $this->getVariantName();

return $this->getIsoName().($region ? ' ('.$region.')' : '').($variant ? ' ('.$variant.')' : '');
}




public function getNativeDescription(): string
{
$region = $this->getRegionName();
$variant = $this->getVariantName();

return $this->getNativeName().($region ? ' ('.$region.')' : '').($variant ? ' ('.$variant.')' : '');
}




public function getFullIsoDescription(): string
{
$region = $this->getRegionName();
$variant = $this->getVariantName();

return $this->getFullIsoName().($region ? ' ('.$region.')' : '').($variant ? ' ('.$variant.')' : '');
}




public function getFullNativeDescription(): string
{
$region = $this->getRegionName();
$variant = $this->getVariantName();

return $this->getFullNativeName().($region ? ' ('.$region.')' : '').($variant ? ' ('.$variant.')' : '');
}




public function __toString(): string
{
return $this->getId();
}




public function jsonSerialize(): string
{
return $this->getIsoDescription();
}
}
