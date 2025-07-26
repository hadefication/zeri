<?php declare(strict_types=1);








namespace PHPUnit\Metadata;

use PHPUnit\Runner\Extension\Extension;

/**
@immutable
@no-named-arguments

*/
final readonly class RequiresPhpunitExtension extends Metadata
{



private string $extensionClass;




public function __construct(int $level, string $extensionClass)
{
parent::__construct($level);

$this->extensionClass = $extensionClass;
}

public function isRequiresPhpunitExtension(): true
{
return true;
}




public function extensionClass(): string
{
return $this->extensionClass;
}
}
