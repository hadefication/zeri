<?php declare(strict_types=1);








namespace PHPUnit\Runner\Extension;

use PHPUnit\TextUI\Configuration\Configuration;

/**
@no-named-arguments
*/
interface Extension
{
public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void;
}
