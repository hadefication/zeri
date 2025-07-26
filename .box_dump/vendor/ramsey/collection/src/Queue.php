<?php











declare(strict_types=1);

namespace Ramsey\Collection;

use Ramsey\Collection\Exception\InvalidArgumentException;
use Ramsey\Collection\Exception\NoSuchElementException;
use Ramsey\Collection\Tool\TypeTrait;
use Ramsey\Collection\Tool\ValueToStringTrait;

use function array_key_first;

/**
@template
@extends
@implements



*/
class Queue extends AbstractArray implements QueueInterface
{
use TypeTrait;
use ValueToStringTrait;








public function __construct(private readonly string $queueType, array $data = [])
{
parent::__construct($data);
}










public function offsetSet(mixed $offset, mixed $value): void
{
if ($this->checkType($this->getType(), $value) === false) {
throw new InvalidArgumentException(
'Value must be of type ' . $this->getType() . '; value is '
. $this->toolValueToString($value),
);
}

$this->data[] = $value;
}




public function add(mixed $element): bool
{
$this[] = $element;

return true;
}






public function element(): mixed
{
return $this->peek() ?? throw new NoSuchElementException(
'Can\'t return element from Queue. Queue is empty.',
);
}

public function offer(mixed $element): bool
{
try {
return $this->add($element);
} catch (InvalidArgumentException) {
return false;
}
}




public function peek(): mixed
{
$index = array_key_first($this->data);

if ($index === null) {
return null;
}

return $this[$index];
}




public function poll(): mixed
{
$index = array_key_first($this->data);

if ($index === null) {
return null;
}

$head = $this[$index];
unset($this[$index]);

return $head;
}






public function remove(): mixed
{
return $this->poll() ?? throw new NoSuchElementException(
'Can\'t return element from Queue. Queue is empty.',
);
}

public function getType(): string
{
return $this->queueType;
}
}
