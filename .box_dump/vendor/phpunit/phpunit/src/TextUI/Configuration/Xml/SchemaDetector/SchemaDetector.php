<?php declare(strict_types=1);








namespace PHPUnit\TextUI\XmlConfiguration;

use PHPUnit\Util\Xml\Loader;
use PHPUnit\Util\Xml\XmlException;

/**
@no-named-arguments


*/
final readonly class SchemaDetector
{



public function detect(string $filename): SchemaDetectionResult
{
$document = (new Loader)->loadFile($filename);

$schemaFinder = new SchemaFinder;

foreach ($schemaFinder->available() as $candidate) {
$schema = (new SchemaFinder)->find($candidate);

if (!(new Validator)->validate($document, $schema)->hasValidationErrors()) {
return new SuccessfulSchemaDetectionResult($candidate);
}
}

return new FailedSchemaDetectionResult;
}
}
