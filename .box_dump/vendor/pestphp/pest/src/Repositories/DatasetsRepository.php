<?php

declare(strict_types=1);

namespace Pest\Repositories;

use Closure;
use Generator;
use Pest\Exceptions\DatasetAlreadyExists;
use Pest\Exceptions\DatasetDoesNotExist;
use Pest\Exceptions\ShouldNotHappen;
use Pest\Support\Exporter;
use Traversable;

use function sprintf;




final class DatasetsRepository
{
private const SEPARATOR = '>>';






private static array $datasets = [];






private static array $withs = [];






public static function set(string $name, Closure|iterable $data, string $scope): void
{
$datasetKey = "$scope".self::SEPARATOR."$name";

if (array_key_exists("$datasetKey", self::$datasets)) {
throw new DatasetAlreadyExists($name, $scope);
}

self::$datasets[$datasetKey] = $data;
}






public static function with(string $filename, string $description, array $with): void
{
self::$withs["$filename".self::SEPARATOR."$description"] = $with;
}

public static function has(string $filename, string $description): bool
{
return array_key_exists($filename.self::SEPARATOR.$description, self::$withs);
}






public static function get(string $filename, string $description): Closure|array 
{
$dataset = self::$withs[$filename.self::SEPARATOR.$description];

$dataset = self::resolve($dataset, $filename);

if ($dataset === null) {
throw ShouldNotHappen::fromMessage('Dataset [%s] not resolvable.');
}

return $dataset;
}







public static function resolve(array $dataset, string $currentTestFile): ?array
{
if ($dataset === []) {
return null;
}

$dataset = self::processDatasets($dataset, $currentTestFile);

$datasetCombinations = self::getDatasetsCombinations($dataset);

$datasetDescriptions = [];
$datasetValues = [];

foreach ($datasetCombinations as $datasetCombination) {
$partialDescriptions = [];
$values = [];

foreach ($datasetCombination as $datasetCombinationElement) {
$partialDescriptions[] = $datasetCombinationElement['label'];

$values = array_merge($values, $datasetCombinationElement['values']);
}

$datasetDescriptions[] = implode(' / ', $partialDescriptions);
$datasetValues[] = $values;
}

foreach (array_count_values($datasetDescriptions) as $descriptionToCheck => $count) {
if ($count > 1) {
$index = 1;
foreach ($datasetDescriptions as $i => $datasetDescription) {
if ($datasetDescription === $descriptionToCheck) {
$datasetDescriptions[$i] .= sprintf(' #%d', $index++);
}
}
}
}

$namedData = [];
foreach ($datasetDescriptions as $i => $datasetDescription) {
$namedData[$datasetDescription] = $datasetValues[$i];
}

return $namedData;
}





private static function processDatasets(array $datasets, string $currentTestFile): array
{
$processedDatasets = [];

foreach ($datasets as $index => $data) {
$processedDataset = [];

if (is_string($data)) {
$datasets[$index] = self::getScopedDataset($data, $currentTestFile);
}

if (is_callable($datasets[$index])) {
$datasets[$index] = call_user_func($datasets[$index]);
}

if ($datasets[$index] instanceof Traversable) {
$preserveKeysForArrayIterator = $datasets[$index] instanceof Generator
&& is_string($datasets[$index]->key());

$datasets[$index] = iterator_to_array($datasets[$index], $preserveKeysForArrayIterator);
}

foreach ($datasets[$index] as $key => $values) {
$values = is_array($values) ? $values : [$values];
$processedDataset[] = [
'label' => self::getDatasetDescription($key, $values),
'values' => $values,
];
}

$processedDatasets[] = $processedDataset;
}

return $processedDatasets;
}




private static function getScopedDataset(string $name, string $currentTestFile): Closure|iterable
{
$matchingDatasets = array_filter(self::$datasets, function (string $key) use ($name, $currentTestFile): bool {
[$datasetScope, $datasetName] = explode(self::SEPARATOR, $key);

if ($name !== $datasetName) {
return false;
}

return str_starts_with($currentTestFile, $datasetScope);
}, ARRAY_FILTER_USE_KEY);

$closestScopeDatasetKey = array_reduce(
array_keys($matchingDatasets),
fn (string|int|null $keyA, string|int|null $keyB): string|int|null => $keyA !== null && strlen((string) $keyA) > strlen((string) $keyB) ? $keyA : $keyB
);

if ($closestScopeDatasetKey === null) {
throw new DatasetDoesNotExist($name);
}

return $matchingDatasets[$closestScopeDatasetKey];
}





private static function getDatasetsCombinations(array $combinations): array
{
$result = [[]];
foreach ($combinations as $index => $values) {
$tmp = [];
foreach ($result as $resultItem) {
foreach ($values as $value) {
$tmp[] = array_merge($resultItem, [$index => $value]);
}
}
$result = $tmp;
}

return $result;
}




private static function getDatasetDescription(int|string $key, array $data): string
{
$exporter = Exporter::default();

if (is_int($key)) {
return sprintf('(%s)', $exporter->shortenedRecursiveExport($data));
}

return sprintf('dataset "%s"', $key);
}
}
