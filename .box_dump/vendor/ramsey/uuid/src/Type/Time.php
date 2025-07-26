<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Type;

use Ramsey\Uuid\Exception\UnsupportedOperationException;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use ValueError;

use function json_decode;
use function json_encode;
use function sprintf;

/**
@immutable





*/
final class Time implements TypeInterface
{
private IntegerObject $seconds;
private IntegerObject $microseconds;

public function __construct(
IntegerObject | float | int | string $seconds,
IntegerObject | float | int | string $microseconds = 0,
) {
$this->seconds = new IntegerObject($seconds);
$this->microseconds = new IntegerObject($microseconds);
}

/**
@pure
*/
public function getSeconds(): IntegerObject
{
return $this->seconds;
}

/**
@pure
*/
public function getMicroseconds(): IntegerObject
{
return $this->microseconds;
}

public function toString(): string
{
return $this->seconds->toString() . '.' . sprintf('%06s', $this->microseconds->toString());
}

public function __toString(): string
{
return $this->toString();
}




public function jsonSerialize(): array
{
return [
'seconds' => $this->getSeconds()->toString(),
'microseconds' => $this->getMicroseconds()->toString(),
];
}

public function serialize(): string
{
return (string) json_encode($this);
}




public function __serialize(): array
{
return [
'seconds' => $this->getSeconds()->toString(),
'microseconds' => $this->getMicroseconds()->toString(),
];
}






public function unserialize(string $data): void
{

$time = json_decode($data, true);

if (!isset($time['seconds']) || !isset($time['microseconds'])) {
throw new UnsupportedOperationException('Attempted to unserialize an invalid value');
}

$this->__construct($time['seconds'], $time['microseconds']);
}




public function __unserialize(array $data): void
{

if (!isset($data['seconds']) || !isset($data['microseconds'])) {
throw new ValueError(sprintf('%s(): Argument #1 ($data) is invalid', __METHOD__));
}


$this->__construct($data['seconds'], $data['microseconds']);
}
}
