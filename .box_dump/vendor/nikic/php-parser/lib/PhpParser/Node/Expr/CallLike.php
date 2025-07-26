<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\VariadicPlaceholder;

abstract class CallLike extends Expr {






abstract public function getRawArgs(): array;




public function isFirstClassCallable(): bool {
$rawArgs = $this->getRawArgs();
return count($rawArgs) === 1 && current($rawArgs) instanceof VariadicPlaceholder;
}






public function getArgs(): array {
assert(!$this->isFirstClassCallable());
return $this->getRawArgs();
}
}
