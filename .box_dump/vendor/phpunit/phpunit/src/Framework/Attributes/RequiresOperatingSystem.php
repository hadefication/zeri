<?php declare(strict_types=1);








namespace PHPUnit\Framework\Attributes;

use Attribute;

/**
@immutable
@no-named-arguments

*/
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final readonly class RequiresOperatingSystem
{



private string $regularExpression;




public function __construct(string $regularExpression)
{
$this->regularExpression = $regularExpression;
}




public function regularExpression(): string
{
return $this->regularExpression;
}
}
