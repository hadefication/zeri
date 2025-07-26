<?php











declare(strict_types=1);

namespace Ramsey\Collection\Map;

/**
@template
@template
@extends



*/
interface TypedMapInterface extends MapInterface
{



public function getKeyType(): string;




public function getValueType(): string;
}
