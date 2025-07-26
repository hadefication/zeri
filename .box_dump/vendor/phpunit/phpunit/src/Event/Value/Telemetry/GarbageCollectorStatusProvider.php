<?php declare(strict_types=1);








namespace PHPUnit\Event\Telemetry;

/**
@no-named-arguments


*/
interface GarbageCollectorStatusProvider
{
public function status(): GarbageCollectorStatus;
}
