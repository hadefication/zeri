<?php declare(strict_types=1);








namespace PHPUnit\TestRunner\TestResult;

use function str_contains;
use PHPUnit\Event\EventFacadeIsSealedException;
use PHPUnit\Event\Facade as EventFacade;
use PHPUnit\Event\UnknownSubscriberTypeException;
use PHPUnit\Runner\DeprecationCollector\Facade as DeprecationCollectorFacade;
use PHPUnit\TestRunner\IssueFilter;
use PHPUnit\TextUI\Configuration\Configuration;
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





public static function result(): TestResult
{
return self::collector()->result();
}





public static function shouldStop(): bool
{
$configuration = ConfigurationRegistry::get();
$collector = self::collector();

if (($configuration->stopOnDefect() || $configuration->stopOnError()) && $collector->hasErroredTests()) {
return true;
}

if (($configuration->stopOnDefect() || $configuration->stopOnFailure()) && $collector->hasFailedTests()) {
return true;
}

if (($configuration->stopOnDefect() || $configuration->stopOnWarning()) && $collector->hasWarnings()) {
return true;
}

if (($configuration->stopOnDefect() || $configuration->stopOnRisky()) && $collector->hasRiskyTests()) {
return true;
}

if (self::stopOnDeprecation($configuration)) {
return true;
}

if ($configuration->stopOnNotice() && $collector->hasNotices()) {
return true;
}

if ($configuration->stopOnIncomplete() && $collector->hasIncompleteTests()) {
return true;
}

if ($configuration->stopOnSkipped() && $collector->hasSkippedTests()) {
return true;
}

return false;
}





private static function collector(): Collector
{
if (self::$collector === null) {
$configuration = ConfigurationRegistry::get();

self::$collector = new Collector(
EventFacade::instance(),
new IssueFilter($configuration->source()),
);
}

return self::$collector;
}

private static function stopOnDeprecation(Configuration $configuration): bool
{
if (!$configuration->stopOnDeprecation()) {
return false;
}

$deprecations = DeprecationCollectorFacade::filteredDeprecations();

if (!$configuration->hasSpecificDeprecationToStopOn()) {
return $deprecations !== [];
}

foreach ($deprecations as $deprecation) {
if (str_contains($deprecation, $configuration->specificDeprecationToStopOn())) {
return true;
}
}

return false;
}
}
