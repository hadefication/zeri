<?php

declare(strict_types=1);

namespace PHPUnit\Architecture\Asserts\Properties;

use PHPUnit\Architecture\Elements\Layer\Layer;
use PHPUnit\Architecture\Enums\Visibility;




trait PropertiesAsserts
{
abstract public static function assertEquals($expected, $actual, string $message = ''): void;






public function assertHasNotPublicProperties($layerA): void
{

$layers = is_array($layerA) ? $layerA : [$layerA];

$result = [];
foreach ($layers as $layer) {
foreach ($layer as $object) {
foreach ($object->properties as $property) {
if ($property->visibility === Visibility::PUBLIC) {
$result[] = "$object->name : {$property->name} <- public";
}
}
}
}

self::assertEquals(
0,
count($result),
'Found public property: ' . implode("\n", $result)
);
}
}
