<?php declare(strict_types = 1);

namespace PHPStan\PhpDocParser\Ast\ConstExpr;

use PHPStan\PhpDocParser\Ast\NodeAttributes;
use function implode;

class ConstExprArrayNode implements ConstExprNode
{

use NodeAttributes;


public array $items;




public function __construct(array $items)
{
$this->items = $items;
}


public function __toString(): string
{
return '[' . implode(', ', $this->items) . ']';
}

}
