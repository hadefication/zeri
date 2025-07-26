<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Type;

use JsonSerializable;
use Serializable;

/**
@immutable


*/
interface TypeInterface extends JsonSerializable, Serializable
{
/**
@pure
*/
public function toString(): string;

/**
@pure
*/
public function __toString(): string;
}
