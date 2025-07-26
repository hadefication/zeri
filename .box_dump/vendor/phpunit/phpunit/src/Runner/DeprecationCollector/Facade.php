<?php declare(strict_types=1);








namespace PHPUnit\Runner\DeprecationCollector;

use PHPUnit\Event\EventFacadeIsSealedException;
use PHPUnit\Event\Facade as EventFacade;
use PHPUnit\Event\UnknownSubscriberTypeException;
use PHPUnit\TestRunner\IssueFilter;
use PHPUnit\TextUI\Configuration\Registry as ConfigurationRegistry;

/**
@no-named-arguments


*/
final class Facade
{
private static ?Collector $collector = null;





public static function init(): void
{
self::collector();
}







public static function deprecations(): array
{
return self::collector()->deprecations();
}







public static function filteredDeprecations(): array
{
return self::collector()->filteredDeprecations();
}





private static function collector(): Collector
{
if (self::$collector === null) {
self::$collector = new Collector(
EventFacade::instance(),
new IssueFilter(
ConfigurationRegistry::get()->source(),
),
);
}

return self::$collector;
}
}
