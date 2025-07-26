<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Rfc4122;

/**
@immutable


*/
trait MaxTrait
{
/**
@pure


*/
abstract public function getBytes(): string;

/**
@pure


*/
public function isMax(): bool
{
return $this->getBytes() === "\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff";
}
}
