<?php declare(strict_types=1);








namespace PHPUnit\Metadata;

/**
@immutable
@no-named-arguments

*/
final readonly class Group extends Metadata
{



private string $groupName;





protected function __construct(int $level, string $groupName)
{
parent::__construct($level);

$this->groupName = $groupName;
}

public function isGroup(): true
{
return true;
}




public function groupName(): string
{
return $this->groupName;
}
}
