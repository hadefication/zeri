<?php

declare(strict_types=1);

namespace League\Flysystem;

use ArrayIterator;
use Generator;
use IteratorAggregate;
use Traversable;

/**
@template
*/
class DirectoryListing implements IteratorAggregate
{



public function __construct(private iterable $listing)
{
}






public function filter(callable $filter): DirectoryListing
{
$generator = (static function (iterable $listing) use ($filter): Generator {
foreach ($listing as $item) {
if ($filter($item)) {
yield $item;
}
}
})($this->listing);

return new DirectoryListing($generator);
}

/**
@template




*/
public function map(callable $mapper): DirectoryListing
{
$generator = (static function (iterable $listing) use ($mapper): Generator {
foreach ($listing as $item) {
yield $mapper($item);
}
})($this->listing);

return new DirectoryListing($generator);
}




public function sortByPath(): DirectoryListing
{
$listing = $this->toArray();

usort($listing, function (StorageAttributes $a, StorageAttributes $b) {
return $a->path() <=> $b->path();
});

return new DirectoryListing($listing);
}




public function getIterator(): Traversable
{
return $this->listing instanceof Traversable
? $this->listing
: new ArrayIterator($this->listing);
}




public function toArray(): array
{
return $this->listing instanceof Traversable
? iterator_to_array($this->listing, false)
: (array) $this->listing;
}
}
