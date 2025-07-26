#!/usr/bin/env php
<?php










declare(strict_types=1);

use Fidry\CpuCoreCounter\Diagnoser;
use Fidry\CpuCoreCounter\Finder\FinderRegistry;

require_once __DIR__.'/../vendor/autoload.php';

echo 'Executing finders...'.PHP_EOL.PHP_EOL;
echo Diagnoser::execute(FinderRegistry::getAllVariants()).PHP_EOL;
