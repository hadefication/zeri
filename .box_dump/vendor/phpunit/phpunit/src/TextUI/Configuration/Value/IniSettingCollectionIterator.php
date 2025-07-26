<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Configuration;

use function count;
use Iterator;

/**
@no-named-arguments
@template-implements

*/
final class IniSettingCollectionIterator implements Iterator
{



private readonly array $iniSettings;
private int $position = 0;

public function __construct(IniSettingCollection $iniSettings)
{
$this->iniSettings = $iniSettings->asArray();
}

public function rewind(): void
{
$this->position = 0;
}

public function valid(): bool
{
return $this->position < count($this->iniSettings);
}

public function key(): int
{
return $this->position;
}

public function current(): IniSetting
{
return $this->iniSettings[$this->position];
}

public function next(): void
{
$this->position++;
}
}
