<?php declare(strict_types=1);








namespace PHPUnit\Event\Telemetry;

use function gc_status;

/**
@no-named-arguments


*/
final readonly class Php83GarbageCollectorStatusProvider implements GarbageCollectorStatusProvider
{
public function status(): GarbageCollectorStatus
{
$status = gc_status();

return new GarbageCollectorStatus(
$status['runs'],
$status['collected'],
$status['threshold'],
$status['roots'],
/**
@phpstan-ignore */
$status['application_time'],
/**
@phpstan-ignore */
$status['collector_time'],
/**
@phpstan-ignore */
$status['destructor_time'],
/**
@phpstan-ignore */
$status['free_time'],
/**
@phpstan-ignore */
$status['running'],
/**
@phpstan-ignore */
$status['protected'],
/**
@phpstan-ignore */
$status['full'],
/**
@phpstan-ignore */
$status['buffer_size'],
);
}
}
