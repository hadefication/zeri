<?php










declare(strict_types=1);

namespace Fidry\CpuCoreCounter;

use Fidry\CpuCoreCounter\Finder\CpuCoreFinder;
use function array_map;
use function explode;
use function implode;
use function max;
use function str_repeat;
use const PHP_EOL;






final class Diagnoser
{





public static function diagnose(array $finders): string
{
$diagnoses = array_map(
static function (CpuCoreFinder $finder): string {
return self::diagnoseFinder($finder);
},
$finders
);

return implode(PHP_EOL, $diagnoses);
}






public static function execute(array $finders): string
{
$diagnoses = array_map(
static function (CpuCoreFinder $finder): string {
$coresCount = $finder->find();

return implode(
'',
[
$finder->toString(),
': ',
null === $coresCount ? 'NULL' : $coresCount,
]
);
},
$finders
);

return implode(PHP_EOL, $diagnoses);
}

private static function diagnoseFinder(CpuCoreFinder $finder): string
{
$diagnosis = $finder->diagnose();

$maxLineLength = max(
array_map(
'strlen',
explode(PHP_EOL, $diagnosis)
)
);

$separator = str_repeat('-', $maxLineLength);

return implode(
'',
[
$finder->toString().':'.PHP_EOL,
$separator.PHP_EOL,
$diagnosis.PHP_EOL,
$separator.PHP_EOL,
]
);
}

private function __construct()
{
}
}
