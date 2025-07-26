<?php declare(strict_types=1);








namespace PHPUnit\TextUI\XmlConfiguration;

use function assert;
use PHPUnit\Runner\Version;
use PHPUnit\Util\Xml\Loader as XmlLoader;
use PHPUnit\Util\Xml\XmlException;

/**
@no-named-arguments


*/
final readonly class Migrator
{





public function migrate(string $filename): string
{
$origin = (new SchemaDetector)->detect($filename);

if (!$origin->detected()) {
throw new Exception('The file does not validate against any known schema');
}

if ($origin->version() === Version::series()) {
throw new Exception('The file does not need to be migrated');
}

$configurationDocument = (new XmlLoader)->loadFile($filename);

foreach ((new MigrationBuilder)->build($origin->version()) as $migration) {
$migration->migrate($configurationDocument);
}

$configurationDocument->formatOutput = true;
$configurationDocument->preserveWhiteSpace = false;

$xml = $configurationDocument->saveXML();

assert($xml !== false);

return $xml;
}
}
