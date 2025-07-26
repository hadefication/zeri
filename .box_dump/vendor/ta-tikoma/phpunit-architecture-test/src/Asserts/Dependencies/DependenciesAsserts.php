<?php

declare(strict_types=1);

namespace PHPUnit\Architecture\Asserts\Dependencies;

use PHPUnit\Architecture\Elements\Layer\Layer;




trait DependenciesAsserts
{
abstract public static function assertNotEquals($expected, $actual, string $message = ''): void;

abstract public static function assertEquals($expected, $actual, string $message = ''): void;







public function assertDoesNotDependOn($layerA, $layerB): void
{
$names = $this->getObjectsWhichUsesOnLayerAFromLayerB($layerA, $layerB);
self::assertEquals(
0,
count($names),
'Found dependencies: ' . implode("\n", $names)
);
}







public function assertDependOn($layerA, $layerB): void
{
$names = $this->getObjectsWhichUsesOnLayerAFromLayerB($layerA, $layerB);
self::assertNotEquals(
0,
count($names),
'Dependencies not found'
);
}









private function getObjectsWhichUsesOnLayerAFromLayerB($layerA, $layerB): array
{

$layers = is_array($layerA) ? $layerA : [$layerA];


$layersToSearch = is_array($layerB) ? $layerB : [$layerB];

$result = [];

foreach ($layers as $layer) {
foreach ($layer as $object) {
foreach ($object->uses as $use) {
foreach ($layersToSearch as $layerToSearch) {

if ($layer->equals($layerToSearch)) {
continue;
}

foreach ($layerToSearch as $objectToSearch) {
if ($objectToSearch->name === $use) {
$result[] = "$object->name <- $objectToSearch->name";
}
}
}
}
}
}

return $result;
}
}
