<?php declare(strict_types = 1);

namespace PHPStan\PhpDocParser\Ast\ConstExpr;

use PHPStan\PhpDocParser\Ast\NodeAttributes;
use function sprintf;
use function str_replace;
use function strlen;
use function substr;

class DoctrineConstExprStringNode extends ConstExprStringNode
{

use NodeAttributes;

public string $value;

public function __construct(string $value)
{
parent::__construct($value, self::DOUBLE_QUOTED);
$this->value = $value;
}

public function __toString(): string
{
return self::escape($this->value);
}

public static function unescape(string $value): string
{

return str_replace('""', '"', substr($value, 1, strlen($value) - 2));
}

private static function escape(string $value): string
{

return sprintf('"%s"', str_replace('"', '""', $value));
}

}
