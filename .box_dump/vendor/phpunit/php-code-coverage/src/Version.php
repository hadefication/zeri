<?php declare(strict_types=1);








namespace SebastianBergmann\CodeCoverage;

use function dirname;
use SebastianBergmann\Version as VersionId;

final class Version
{
private static string $version = '';

public static function id(): string
{
if (self::$version === '') {
self::$version = (new VersionId('11.0.10', dirname(__DIR__)))->asString();
}

return self::$version;
}
}
