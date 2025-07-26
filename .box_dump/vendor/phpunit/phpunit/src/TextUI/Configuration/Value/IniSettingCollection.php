<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Configuration;

use function count;
use Countable;
use IteratorAggregate;

/**
@no-named-arguments
@immutable
@template-implements


*/
final readonly class IniSettingCollection implements Countable, IteratorAggregate
{



private array $iniSettings;




public static function fromArray(array $iniSettings): self
{
return new self(...$iniSettings);
}

private function __construct(IniSetting ...$iniSettings)
{
$this->iniSettings = $iniSettings;
}




public function asArray(): array
{
return $this->iniSettings;
}

public function count(): int
{
return count($this->iniSettings);
}

public function getIterator(): IniSettingCollectionIterator
{
return new IniSettingCollectionIterator($this);
}
}
