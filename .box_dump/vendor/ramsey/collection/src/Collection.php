<?php











declare(strict_types=1);

namespace Ramsey\Collection;

/**
@template
@extends























































*/
class Collection extends AbstractCollection
{








public function __construct(private readonly string $collectionType, array $data = [])
{
parent::__construct($data);
}

public function getType(): string
{
return $this->collectionType;
}
}
