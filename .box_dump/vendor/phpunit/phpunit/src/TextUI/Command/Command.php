<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Command;

/**
@no-named-arguments


*/
interface Command
{
public function execute(): Result;
}
