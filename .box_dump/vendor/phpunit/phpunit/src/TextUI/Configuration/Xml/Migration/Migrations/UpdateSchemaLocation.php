<?php declare(strict_types=1);








namespace PHPUnit\TextUI\XmlConfiguration;

use function assert;
use function str_contains;
use DOMDocument;
use DOMElement;
use PHPUnit\Runner\Version;

/**
@no-named-arguments


*/
final readonly class UpdateSchemaLocation implements Migration
{
private const NAMESPACE_URI = 'http://www.w3.org/2001/XMLSchema-instance';
private const LOCAL_NAME_SCHEMA_LOCATION = 'noNamespaceSchemaLocation';

public function migrate(DOMDocument $document): void
{
$root = $document->documentElement;

assert($root instanceof DOMElement);

$existingSchemaLocation = $root->getAttributeNodeNS(self::NAMESPACE_URI, self::LOCAL_NAME_SCHEMA_LOCATION)->value;

if (str_contains($existingSchemaLocation, '://') === false) { 
return;
}

$root->setAttributeNS(
self::NAMESPACE_URI,
'xsi:' . self::LOCAL_NAME_SCHEMA_LOCATION,
'https://schema.phpunit.de/' . Version::series() . '/phpunit.xsd',
);
}
}
