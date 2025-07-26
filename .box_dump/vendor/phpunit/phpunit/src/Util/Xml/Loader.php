<?php declare(strict_types=1);








namespace PHPUnit\Util\Xml;

use function error_reporting;
use function file_get_contents;
use function libxml_get_errors;
use function libxml_use_internal_errors;
use function sprintf;
use function trim;
use DOMDocument;

/**
@no-named-arguments


*/
final readonly class Loader
{



public function loadFile(string $filename): DOMDocument
{
$reporting = error_reporting(0);
$contents = file_get_contents($filename);

error_reporting($reporting);

if ($contents === false) {
throw new XmlException(
sprintf(
'Could not read XML from file "%s"',
$filename,
),
);
}

if (trim($contents) === '') {
throw new XmlException(
sprintf(
'Could not parse XML from empty file "%s"',
$filename,
),
);
}

return $this->load($contents);
}




public function load(string $actual): DOMDocument
{
if ($actual === '') {
throw new XmlException('Could not parse XML from empty string');
}

$document = new DOMDocument;
$document->preserveWhiteSpace = false;

$internal = libxml_use_internal_errors(true);
$message = '';
$reporting = error_reporting(0);
$loaded = $document->loadXML($actual);

foreach (libxml_get_errors() as $error) {
$message .= "\n" . $error->message;
}

libxml_use_internal_errors($internal);
error_reporting($reporting);

if ($loaded === false) {
if ($message === '') {
$message = 'Could not load XML for unknown reason';
}

throw new XmlException($message);
}

return $document;
}
}
