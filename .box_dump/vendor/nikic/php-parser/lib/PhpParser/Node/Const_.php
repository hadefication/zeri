<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\NodeAbstract;

class Const_ extends NodeAbstract {

public Identifier $name;

public Expr $value;


public ?Name $namespacedName;








public function __construct($name, Expr $value, array $attributes = []) {
$this->attributes = $attributes;
$this->name = \is_string($name) ? new Identifier($name) : $name;
$this->value = $value;
}

public function getSubNodeNames(): array {
return ['name', 'value'];
}

public function getType(): string {
return 'Const';
}
}
