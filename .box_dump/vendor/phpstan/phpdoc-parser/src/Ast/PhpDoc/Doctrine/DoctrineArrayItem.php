<?php declare(strict_types = 1);

namespace PHPStan\PhpDocParser\Ast\PhpDoc\Doctrine;

use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprIntegerNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprStringNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstFetchNode;
use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\NodeAttributes;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;

/**
@phpstan-import-type
@phpstan-type
*/
class DoctrineArrayItem implements Node
{

use NodeAttributes;


public $key;


public $value;





public function __construct($key, $value)
{
$this->key = $key;
$this->value = $value;
}


public function __toString(): string
{
if ($this->key === null) {
return (string) $this->value;
}

return $this->key . '=' . $this->value;
}

}
