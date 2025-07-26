<?php











declare(strict_types=1);

namespace Ramsey\Uuid;

use JsonSerializable;
use Ramsey\Uuid\Fields\FieldsInterface;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Serializable;
use Stringable;

/**
@immutable


*/
interface UuidInterface extends
DeprecatedUuidInterface,
JsonSerializable,
Serializable,
Stringable
{










public function compareTo(UuidInterface $other): int;











public function equals(?object $other): bool;

/**
@pure




*/
public function getBytes(): string;




public function getFields(): FieldsInterface;




public function getHex(): Hexadecimal;




public function getInteger(): IntegerObject;









public function getUrn(): string;

/**
@pure




*/
public function toString(): string;

/**
@pure




*/
public function __toString(): string;
}
