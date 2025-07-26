<?php declare(strict_types=1);








namespace PHPUnit\TextUI\XmlConfiguration;

use function assert;
use function file_get_contents;
use function libxml_clear_errors;
use function libxml_get_errors;
use function libxml_use_internal_errors;
use DOMDocument;

/**
@no-named-arguments


*/
final readonly class Validator
{
public function validate(DOMDocument $document, string $xsdFilename): ValidationResult
{
$buffer = file_get_contents($xsdFilename);

assert($buffer !== false);

$originalErrorHandling = libxml_use_internal_errors(true);

$document->schemaValidateSource($buffer);

$errors = libxml_get_errors();
libxml_clear_errors();
libxml_use_internal_errors($originalErrorHandling);

return ValidationResult::fromArray($errors);
}
}
