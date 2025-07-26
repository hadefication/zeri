<?php declare(strict_types=1);








namespace PHPUnit\Framework;

/**
@no-named-arguments


*/
interface Reorderable
{
public function sortId(): string;




public function provides(): array;




public function requires(): array;
}
