<?php











declare(strict_types=1);

namespace Ramsey\Collection\Map;

use Ramsey\Collection\Exception\InvalidArgumentException;
use Ramsey\Collection\Tool\TypeTrait;
use Ramsey\Collection\Tool\ValueToStringTrait;

/**
@template
@template
@extends
@implements



*/
abstract class AbstractTypedMap extends AbstractMap implements TypedMapInterface
{
use TypeTrait;
use ValueToStringTrait;







public function offsetSet(mixed $offset, mixed $value): void
{
if ($this->checkType($this->getKeyType(), $offset) === false) {
throw new InvalidArgumentException(
'Key must be of type ' . $this->getKeyType() . '; key is '
. $this->toolValueToString($offset),
);
}

if ($this->checkType($this->getValueType(), $value) === false) {
throw new InvalidArgumentException(
'Value must be of type ' . $this->getValueType() . '; value is '
. $this->toolValueToString($value),
);
}

parent::offsetSet($offset, $value);
}
}
