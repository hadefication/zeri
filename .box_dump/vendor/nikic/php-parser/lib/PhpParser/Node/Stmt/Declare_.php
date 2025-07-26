<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;
use PhpParser\Node\DeclareItem;

class Declare_ extends Node\Stmt {

public array $declares;

public ?array $stmts;








public function __construct(array $declares, ?array $stmts = null, array $attributes = []) {
$this->attributes = $attributes;
$this->declares = $declares;
$this->stmts = $stmts;
}

public function getSubNodeNames(): array {
return ['declares', 'stmts'];
}

public function getType(): string {
return 'Stmt_Declare';
}
}
