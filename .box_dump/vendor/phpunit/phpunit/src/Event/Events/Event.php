<?php declare(strict_types=1);








namespace PHPUnit\Event;

/**
@no-named-arguments
*/
interface Event
{
public function telemetryInfo(): Telemetry\Info;

public function asString(): string;
}
