<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Configuration;

use function assert;
use function file_get_contents;
use function file_put_contents;
use function serialize;
use function unserialize;
use PHPUnit\Event\Facade as EventFacade;
use PHPUnit\TextUI\CliArguments\Configuration as CliConfiguration;
use PHPUnit\TextUI\CliArguments\Exception;
use PHPUnit\TextUI\XmlConfiguration\Configuration as XmlConfiguration;
use PHPUnit\Util\VersionComparisonOperator;

/**
@no-named-arguments





*/
final class Registry
{
private static ?Configuration $instance = null;

public static function saveTo(string $path): bool
{
$result = file_put_contents(
$path,
serialize(self::get()),
);

if ($result) {
return true;
}


return false;

}

/**
@noinspection




*/
public static function loadFrom(string $path): void
{
$buffer = file_get_contents($path);

assert($buffer !== false);

self::$instance = unserialize(
$buffer,
[
'allowed_classes' => [
Configuration::class,
Php::class,
ConstantCollection::class,
Constant::class,
IniSettingCollection::class,
IniSetting::class,
VariableCollection::class,
Variable::class,
DirectoryCollection::class,
Directory::class,
FileCollection::class,
File::class,
FilterDirectoryCollection::class,
FilterDirectory::class,
TestDirectoryCollection::class,
TestDirectory::class,
TestFileCollection::class,
TestFile::class,
TestSuiteCollection::class,
TestSuite::class,
VersionComparisonOperator::class,
Source::class,
],
],
);
}

public static function get(): Configuration
{
assert(self::$instance instanceof Configuration);

return self::$instance;
}






public static function init(CliConfiguration $cliConfiguration, XmlConfiguration $xmlConfiguration): Configuration
{
self::$instance = (new Merger)->merge($cliConfiguration, $xmlConfiguration);

EventFacade::emitter()->testRunnerConfigured(self::$instance);

return self::$instance;
}
}
