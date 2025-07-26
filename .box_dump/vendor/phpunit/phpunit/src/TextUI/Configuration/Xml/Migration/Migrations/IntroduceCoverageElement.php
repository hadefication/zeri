<?php declare(strict_types=1);








namespace PHPUnit\TextUI\XmlConfiguration;

use DOMDocument;

/**
@no-named-arguments


*/
final readonly class IntroduceCoverageElement implements Migration
{
public function migrate(DOMDocument $document): void
{
$coverage = $document->createElement('coverage');

$document->documentElement->insertBefore(
$coverage,
$document->documentElement->firstChild,
);
}
}
