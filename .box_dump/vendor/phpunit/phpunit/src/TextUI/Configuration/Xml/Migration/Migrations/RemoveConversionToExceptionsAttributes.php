<?php declare(strict_types=1);








namespace PHPUnit\TextUI\XmlConfiguration;

use function assert;
use DOMDocument;
use DOMElement;

/**
@no-named-arguments


*/
final readonly class RemoveConversionToExceptionsAttributes implements Migration
{
public function migrate(DOMDocument $document): void
{
$root = $document->documentElement;

assert($root instanceof DOMElement);

if ($root->hasAttribute('convertDeprecationsToExceptions')) {
$root->removeAttribute('convertDeprecationsToExceptions');
}

if ($root->hasAttribute('convertErrorsToExceptions')) {
$root->removeAttribute('convertErrorsToExceptions');
}

if ($root->hasAttribute('convertNoticesToExceptions')) {
$root->removeAttribute('convertNoticesToExceptions');
}

if ($root->hasAttribute('convertWarningsToExceptions')) {
$root->removeAttribute('convertWarningsToExceptions');
}
}
}
