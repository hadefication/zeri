<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\Node;

class NullableType extends ComplexType {

public Node $type;







public function __construct(Node $type, array $attributes = []) {
$this->attributes = $attributes;
$this->type = $type;
}

public function getSubNodeNames(): array {
return ['type'];
}

public function getType(): string {
return 'NullableType';
}
}
