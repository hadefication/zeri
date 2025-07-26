<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\NodeAbstract;

class InterpolatedStringPart extends NodeAbstract {

public string $value;







public function __construct(string $value, array $attributes = []) {
$this->attributes = $attributes;
$this->value = $value;
}

public function getSubNodeNames(): array {
return ['value'];
}

public function getType(): string {
return 'InterpolatedStringPart';
}
}


class_alias(InterpolatedStringPart::class, Scalar\EncapsedStringPart::class);
