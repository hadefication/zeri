#!/usr/bin/env php
<?php










declare(strict_types=1);

use Fidry\CpuCoreCounter\CpuCoreCounter;
use Fidry\CpuCoreCounter\Finder\FinderRegistry;

require_once __DIR__.'/../vendor/autoload.php';

$separator = str_repeat('–', 80);

echo 'With all finders...'.PHP_EOL.PHP_EOL;
echo (new CpuCoreCounter(FinderRegistry::getAllVariants()))->trace().PHP_EOL;
echo $separator.PHP_EOL.PHP_EOL;

echo 'Logical CPU cores finders...'.PHP_EOL.PHP_EOL;
echo (new CpuCoreCounter(FinderRegistry::getDefaultLogicalFinders()))->trace().PHP_EOL;
echo $separator.PHP_EOL.PHP_EOL;

echo 'Physical CPU cores finders...'.PHP_EOL.PHP_EOL;
echo (new CpuCoreCounter(FinderRegistry::getDefaultPhysicalFinders()))->trace().PHP_EOL;
echo $separator.PHP_EOL.PHP_EOL;
