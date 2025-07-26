<?php declare(strict_types=1);

namespace PhpParser\Node\Scalar;

use PhpParser\Node\Expr;
use PhpParser\Node\InterpolatedStringPart;
use PhpParser\Node\Scalar;

class InterpolatedString extends Scalar {

public array $parts;







public function __construct(array $parts, array $attributes = []) {
$this->attributes = $attributes;
$this->parts = $parts;
}

public function getSubNodeNames(): array {
return ['parts'];
}

public function getType(): string {
return 'Scalar_InterpolatedString';
}
}


class_alias(InterpolatedString::class, Encapsed::class);
