<?php declare(strict_types=1);








namespace PHPUnit\Util;

use PHPUnit\TextUI\Configuration\Registry as ConfigurationRegistry;
use SebastianBergmann\Exporter\Exporter as OriginalExporter;

/**
@no-named-arguments
*/
final class Exporter
{
private static ?OriginalExporter $exporter = null;

public static function export(mixed $value): string
{
return self::exporter()->export($value);
}




public static function shortenedRecursiveExport(array $data): string
{
return self::exporter()->shortenedRecursiveExport($data);
}

public static function shortenedExport(mixed $value): string
{
return self::exporter()->shortenedExport($value);
}

private static function exporter(): OriginalExporter
{
if (self::$exporter !== null) {
return self::$exporter;
}

self::$exporter = new OriginalExporter(
ConfigurationRegistry::get()->shortenArraysForExportThreshold(),
);

return self::$exporter;
}
}
