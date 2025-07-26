<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node\Stmt;

class Block extends Stmt {

public array $stmts;







public function __construct(array $stmts, array $attributes = []) {
$this->attributes = $attributes;
$this->stmts = $stmts;
}

public function getType(): string {
return 'Stmt_Block';
}

public function getSubNodeNames(): array {
return ['stmts'];
}
}
