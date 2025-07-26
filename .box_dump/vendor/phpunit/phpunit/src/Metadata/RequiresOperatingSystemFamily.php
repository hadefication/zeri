<?php declare(strict_types=1);








namespace PHPUnit\Metadata;

/**
@immutable
@no-named-arguments

*/
final readonly class RequiresOperatingSystemFamily extends Metadata
{



private string $operatingSystemFamily;





protected function __construct(int $level, string $operatingSystemFamily)
{
parent::__construct($level);

$this->operatingSystemFamily = $operatingSystemFamily;
}

public function isRequiresOperatingSystemFamily(): true
{
return true;
}




public function operatingSystemFamily(): string
{
return $this->operatingSystemFamily;
}
}
