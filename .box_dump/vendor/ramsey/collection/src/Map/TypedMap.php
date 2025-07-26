<?php











declare(strict_types=1);

namespace Ramsey\Collection\Map;

/**
@template
@template
@extends































































*/
class TypedMap extends AbstractTypedMap
{








public function __construct(
private readonly string $keyType,
private readonly string $valueType,
array $data = [],
) {
parent::__construct($data);
}

public function getKeyType(): string
{
return $this->keyType;
}

public function getValueType(): string
{
return $this->valueType;
}
}
