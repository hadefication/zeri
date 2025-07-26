<?php declare(strict_types=1);








namespace PHPUnit\Metadata;

/**
@immutable
@no-named-arguments

*/
final readonly class RequiresOperatingSystem extends Metadata
{



private string $operatingSystem;





public function __construct(int $level, string $operatingSystem)
{
parent::__construct($level);

$this->operatingSystem = $operatingSystem;
}

public function isRequiresOperatingSystem(): true
{
return true;
}




public function operatingSystem(): string
{
return $this->operatingSystem;
}
}
