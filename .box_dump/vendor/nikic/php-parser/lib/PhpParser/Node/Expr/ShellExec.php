<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;
use PhpParser\Node\InterpolatedStringPart;

class ShellExec extends Expr {

public array $parts;







public function __construct(array $parts, array $attributes = []) {
$this->attributes = $attributes;
$this->parts = $parts;
}

public function getSubNodeNames(): array {
return ['parts'];
}

public function getType(): string {
return 'Expr_ShellExec';
}
}
