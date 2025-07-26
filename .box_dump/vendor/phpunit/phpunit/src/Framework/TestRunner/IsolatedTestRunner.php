<?php declare(strict_types=1);








namespace PHPUnit\Framework;

/**
@no-named-arguments


*/
interface IsolatedTestRunner
{
public function run(TestCase $test, bool $runEntireClass, bool $preserveGlobalState): void;
}
