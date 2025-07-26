<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node\Stmt;
use PhpParser\Node\UseItem;

class Use_ extends Stmt {





public const TYPE_UNKNOWN = 0;

public const TYPE_NORMAL = 1;

public const TYPE_FUNCTION = 2;

public const TYPE_CONSTANT = 3;


public int $type;

public array $uses;








public function __construct(array $uses, int $type = self::TYPE_NORMAL, array $attributes = []) {
$this->attributes = $attributes;
$this->type = $type;
$this->uses = $uses;
}

public function getSubNodeNames(): array {
return ['type', 'uses'];
}

public function getType(): string {
return 'Stmt_Use';
}
}
