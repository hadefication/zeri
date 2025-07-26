<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr;

class List_ extends Expr {

public const KIND_LIST = 1; 
public const KIND_ARRAY = 2; 


public array $items;







public function __construct(array $items, array $attributes = []) {
$this->attributes = $attributes;
$this->items = $items;
}

public function getSubNodeNames(): array {
return ['items'];
}

public function getType(): string {
return 'Expr_List';
}
}
