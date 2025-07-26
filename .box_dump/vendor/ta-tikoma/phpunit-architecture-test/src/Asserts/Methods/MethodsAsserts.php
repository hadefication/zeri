<?php

declare(strict_types=1);

namespace PHPUnit\Architecture\Asserts\Methods;

use PHPUnit\Architecture\Elements\Layer\Layer;




trait MethodsAsserts
{
abstract public static function assertNotEquals($expected, $actual, string $message = ''): void;

abstract public static function assertEquals($expected, $actual, string $message = ''): void;








public function assertIncomingsNotFrom($layerA, $layerB, array $methods = []): void
{
$incomings = $this->getIncomingsFrom($layerA, $layerB, $methods);

self::assertEquals(
0,
count($incomings),
'Found incomings: ' . implode("\n", $incomings)
);
}








public function assertIncomingsFrom($layerA, $layerB, array $methods = []): void
{
$incomings = $this->getIncomingsFrom($layerA, $layerB, $methods);

self::assertNotEquals(
0,
count($incomings),
'Not found incomings'
);
}








protected function getIncomingsFrom($layerA, $layerB, array $methods): array
{

$layers = is_array($layerA) ? $layerA : [$layerA];


$layersToSearch = is_array($layerB) ? $layerB : [$layerB];

$result = [];

foreach ($layers as $layer) {
foreach ($layer as $object) {
foreach ($object->methods as $method) {
if (count($methods) > 0) {
if (!in_array($method->name, $methods)) {
continue;
}
}

foreach ($method->args as list($aType, $aName)) {
$types = is_array($aType) ? $aType : [$aType];
foreach ($types as $type) {
foreach ($layersToSearch as $layerToSearch) {

if ($layer->equals($layerToSearch)) {
continue;
}

foreach ($layerToSearch as $objectToSearch) {
if ($objectToSearch->name === $type) {
$result[] = "{$object->name}: {$method->name} -> $aName <- {$objectToSearch->name}";
}
}
}
}
}
}
}
}

return $result;
}








public function assertOutgoingFrom($layerA, $layerB, array $methods = []): void
{
$outgoings = $this->getOutgoingFrom($layerA, $layerB, $methods);

self::assertNotEquals(
0,
count($outgoings),
'Outgoings not found'
);
}








public function assertOutgoingNotFrom($layerA, $layerB, array $methods = []): void
{
$outgoings = $this->getOutgoingFrom($layerA, $layerB, $methods);

self::assertNotEquals(
0,
count($outgoings),
'Found outgoings: ' . implode("\n", $outgoings)
);
}








protected function getOutgoingFrom($layerA, $layerB, array $methods): array
{

$layers = is_array($layerA) ? $layerA : [$layerA];


$layersToSearch = is_array($layerB) ? $layerB : [$layerB];

$result = [];

foreach ($layers as $layer) {
foreach ($layer as $object) {
foreach ($object->methods as $method) {
if (count($methods) > 0) {
if (!in_array($method->name, $methods)) {
continue;
}
}

foreach ($layersToSearch as $layerToSearch) {

if ($layer->equals($layerToSearch)) {
continue;
}

foreach ($layerToSearch as $objectToSearch) {
if ($objectToSearch->name === $method->return) {
$result[] = "{$object->name}: {$method->name} -> {$method->return} <- {$objectToSearch->name}";
}
}
}
}
}
}

return $result;
}






public function assertMethodSizeLessThan($layerA, int $size): void
{

$layers = is_array($layerA) ? $layerA : [$layerA];

$result = [];
foreach ($layers as $layer) {
foreach ($layer as $object) {
foreach ($object->methods as $method) {
if ($method->size > $size) {
$result[] = "{$object->name}: {$method->name} -> {$method->size} <- $size";
}
}
}
}

self::assertEquals(
0,
count($result),
'Found large methods: ' . implode("\n", $result)
);
}
}
