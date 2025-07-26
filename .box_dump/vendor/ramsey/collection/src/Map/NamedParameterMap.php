<?php











declare(strict_types=1);

namespace Ramsey\Collection\Map;

use Ramsey\Collection\Exception\InvalidArgumentException;
use Ramsey\Collection\Tool\TypeTrait;
use Ramsey\Collection\Tool\ValueToStringTrait;

use function array_combine;
use function array_key_exists;
use function is_int;

/**
@extends



*/
class NamedParameterMap extends AbstractMap
{
use TypeTrait;
use ValueToStringTrait;






private readonly array $namedParameters;







public function __construct(array $namedParameters, array $data = [])
{
$this->namedParameters = $this->filterNamedParameters($namedParameters);
parent::__construct($data);
}






public function getNamedParameters(): array
{
return $this->namedParameters;
}

public function offsetSet(mixed $offset, mixed $value): void
{
if (!array_key_exists($offset, $this->namedParameters)) {
throw new InvalidArgumentException(
'Attempting to set value for unconfigured parameter \''
. $this->toolValueToString($offset) . '\'',
);
}

if ($this->checkType($this->namedParameters[$offset], $value) === false) {
throw new InvalidArgumentException(
'Value for \'' . $offset . '\' must be of type '
. $this->namedParameters[$offset] . '; value is '
. $this->toolValueToString($value),
);
}

$this->data[$offset] = $value;
}









protected function filterNamedParameters(array $namedParameters): array
{
$names = [];
$types = [];

foreach ($namedParameters as $key => $value) {
if (is_int($key)) {
$names[] = $value;
$types[] = 'mixed';
} else {
$names[] = $key;
$types[] = $value;
}
}

return array_combine($names, $types) ?: [];
}
}
