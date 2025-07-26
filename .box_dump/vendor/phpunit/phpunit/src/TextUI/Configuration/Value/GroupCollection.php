<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Configuration;

use IteratorAggregate;

/**
@no-named-arguments
@immutable
@template-implements


*/
final readonly class GroupCollection implements IteratorAggregate
{



private array $groups;




public static function fromArray(array $groups): self
{
return new self(...$groups);
}

private function __construct(Group ...$groups)
{
$this->groups = $groups;
}




public function asArray(): array
{
return $this->groups;
}




public function asArrayOfStrings(): array
{
$result = [];

foreach ($this->groups as $group) {
$result[] = $group->name();
}

return $result;
}

public function isEmpty(): bool
{
return empty($this->groups);
}

public function getIterator(): GroupCollectionIterator
{
return new GroupCollectionIterator($this);
}
}
