<?php











declare(strict_types=1);

namespace Ramsey\Collection;

/**
@template
@extends




















*/
class Set extends AbstractSet
{







public function __construct(private readonly string $setType, array $data = [])
{
parent::__construct($data);
}

public function getType(): string
{
return $this->setType;
}
}
