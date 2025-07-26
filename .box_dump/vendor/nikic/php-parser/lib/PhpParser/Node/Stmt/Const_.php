<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Const_ extends Node\Stmt {

public array $consts;

public array $attrGroups;








public function __construct(
array $consts,
array $attributes = [],
array $attrGroups = []
) {
$this->attributes = $attributes;
$this->attrGroups = $attrGroups;
$this->consts = $consts;
}

public function getSubNodeNames(): array {
return ['attrGroups', 'consts'];
}

public function getType(): string {
return 'Stmt_Const';
}
}
