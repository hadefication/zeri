<?php declare(strict_types=1);








namespace PHPUnit\Framework;

use Countable;

/**
@no-named-arguments
*/
interface Test extends Countable
{
public function run(): void;
}
