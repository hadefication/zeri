<?php










use Castor\Attribute\AsRawTokens;
use Castor\Attribute\AsTask;

use function Castor\import;
use function Castor\mount;
use function Castor\run;

import(__DIR__ . '/tools/php-cs-fixer/castor.php');
import(__DIR__ . '/tools/phpstan/castor.php');

mount(__DIR__ . '/tools/phar');

#[AsTask(description: 'Install dependencies')]
function install(): void
{
run(['composer', 'install'], workingDirectory: __DIR__);
qa\cs\install();
qa\phpstan\install();
}

#[AsTask(description: 'Run PHPUnit', ignoreValidationErrors: true)]
function phpunit(#[AsRawTokens] array $rawTokens): void
{
run(['vendor/bin/simple-phpunit', ...$rawTokens]);
}
