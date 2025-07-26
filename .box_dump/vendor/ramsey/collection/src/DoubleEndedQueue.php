<?php











declare(strict_types=1);

namespace Ramsey\Collection;

use Ramsey\Collection\Exception\InvalidArgumentException;
use Ramsey\Collection\Exception\NoSuchElementException;

use function array_key_last;
use function array_pop;
use function array_unshift;

/**
@template
@extends
@implements



*/
class DoubleEndedQueue extends Queue implements DoubleEndedQueueInterface
{







public function __construct(private readonly string $queueType, array $data = [])
{
parent::__construct($this->queueType, $data);
}




public function addFirst(mixed $element): bool
{
if ($this->checkType($this->getType(), $element) === false) {
throw new InvalidArgumentException(
'Value must be of type ' . $this->getType() . '; value is '
. $this->toolValueToString($element),
);
}

array_unshift($this->data, $element);

return true;
}




public function addLast(mixed $element): bool
{
return $this->add($element);
}

public function offerFirst(mixed $element): bool
{
try {
return $this->addFirst($element);
} catch (InvalidArgumentException) {
return false;
}
}

public function offerLast(mixed $element): bool
{
return $this->offer($element);
}






public function removeFirst(): mixed
{
return $this->remove();
}






public function removeLast(): mixed
{
return $this->pollLast() ?? throw new NoSuchElementException(
'Can\'t return element from Queue. Queue is empty.',
);
}




public function pollFirst(): mixed
{
return $this->poll();
}




public function pollLast(): mixed
{
return array_pop($this->data);
}






public function firstElement(): mixed
{
return $this->element();
}






public function lastElement(): mixed
{
return $this->peekLast() ?? throw new NoSuchElementException(
'Can\'t return element from Queue. Queue is empty.',
);
}




public function peekFirst(): mixed
{
return $this->peek();
}




public function peekLast(): mixed
{
$lastIndex = array_key_last($this->data);

if ($lastIndex === null) {
return null;
}

return $this->data[$lastIndex];
}
}
