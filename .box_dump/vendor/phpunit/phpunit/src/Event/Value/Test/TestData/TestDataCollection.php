<?php declare(strict_types=1);








namespace PHPUnit\Event\TestData;

use function count;
use Countable;
use IteratorAggregate;

/**
@template-implements
@no-named-arguments

*/
final readonly class TestDataCollection implements Countable, IteratorAggregate
{



private array $data;
private ?DataFromDataProvider $fromDataProvider;




public static function fromArray(array $data): self
{
return new self(...$data);
}

private function __construct(TestData ...$data)
{
$fromDataProvider = null;

foreach ($data as $_data) {
if ($_data->isFromDataProvider()) {
$fromDataProvider = $_data;
}
}

$this->data = $data;
$this->fromDataProvider = $fromDataProvider;
}




public function asArray(): array
{
return $this->data;
}

public function count(): int
{
return count($this->data);
}

/**
@phpstan-assert-if-true
*/
public function hasDataFromDataProvider(): bool
{
return $this->fromDataProvider !== null;
}




public function dataFromDataProvider(): DataFromDataProvider
{
if (!$this->hasDataFromDataProvider()) {
throw new NoDataSetFromDataProviderException;
}

return $this->fromDataProvider;
}

public function getIterator(): TestDataCollectionIterator
{
return new TestDataCollectionIterator($this);
}
}
