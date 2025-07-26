<?php











declare(strict_types=1);

namespace Ramsey\Uuid;

use DateTimeInterface;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\Validator\ValidatorInterface;




interface UuidFactoryInterface
{
/**
@pure






*/
public function fromBytes(string $bytes): UuidInterface;











public function fromDateTime(
DateTimeInterface $dateTime,
?Hexadecimal $node = null,
?int $clockSeq = null,
): UuidInterface;

/**
@pure






*/
public function fromInteger(string $integer): UuidInterface;

/**
@pure






*/
public function fromString(string $uuid): UuidInterface;




public function getValidator(): ValidatorInterface;











public function uuid1($node = null, ?int $clockSeq = null): UuidInterface;















public function uuid2(
int $localDomain,
?IntegerObject $localIdentifier = null,
?Hexadecimal $node = null,
?int $clockSeq = null,
): UuidInterface;

/**
@pure







*/
public function uuid3($ns, string $name): UuidInterface;






public function uuid4(): UuidInterface;

/**
@pure







*/
public function uuid5($ns, string $name): UuidInterface;










public function uuid6(?Hexadecimal $node = null, ?int $clockSeq = null): UuidInterface;
}
