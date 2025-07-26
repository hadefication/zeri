<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Namespace_ extends Node\Stmt {

public const KIND_SEMICOLON = 1;
public const KIND_BRACED = 2;


public ?Node\Name $name;

public $stmts;








public function __construct(?Node\Name $name = null, ?array $stmts = [], array $attributes = []) {
$this->attributes = $attributes;
$this->name = $name;
$this->stmts = $stmts;
}

public function getSubNodeNames(): array {
return ['name', 'stmts'];
}

public function getType(): string {
return 'Stmt_Namespace';
}
}
