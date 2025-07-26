<?php

declare(strict_types=1);

namespace Pest\Support;

use SebastianBergmann\Exporter\Exporter as BaseExporter;
use SebastianBergmann\RecursionContext\Context;




final readonly class Exporter
{



private const MAX_ARRAY_ITEMS = 3;




public function __construct(
private BaseExporter $exporter,
) {

}




public static function default(): self
{
return new self(
new BaseExporter
);
}






public function shortenedRecursiveExport(array &$data, ?Context $context = null): string
{
$result = [];
$array = $data;
$itemsCount = 0;
$exporter = self::default();
$context ??= new Context;

$context->add($data);

foreach ($array as $key => $value) {
if (++$itemsCount > self::MAX_ARRAY_ITEMS) {
$result[] = '…';

break;
}

if (! is_array($value)) {
$result[] = $exporter->shortenedExport($value);

continue;
}

$result[] = $context->contains($data[$key]) !== false
? '*RECURSION*'

: sprintf('[%s]', $this->shortenedRecursiveExport($data[$key], $context));
}

return implode(', ', $result);
}




public function shortenedExport(mixed $value): string
{
$map = [
'#\.{3}#' => '…',
'#\\\n\s*#' => '',
'# Object \(…\)#' => '',
];

return (string) preg_replace(array_keys($map), array_values($map), $this->exporter->shortenedExport($value));
}
}
