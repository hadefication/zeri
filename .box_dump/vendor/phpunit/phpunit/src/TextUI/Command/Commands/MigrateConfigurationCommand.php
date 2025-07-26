<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Command;

use const PHP_EOL;
use function copy;
use function file_put_contents;
use function sprintf;
use PHPUnit\TextUI\XmlConfiguration\Migrator;
use Throwable;

/**
@no-named-arguments


*/
final readonly class MigrateConfigurationCommand implements Command
{
private string $filename;

public function __construct(string $filename)
{
$this->filename = $filename;
}

public function execute(): Result
{
try {
$migrated = (new Migrator)->migrate($this->filename);

copy($this->filename, $this->filename . '.bak');

file_put_contents($this->filename, $migrated);

return Result::from(
sprintf(
'Created backup:         %s.bak%sMigrated configuration: %s%s',
$this->filename,
PHP_EOL,
$this->filename,
PHP_EOL,
),
);
} catch (Throwable $t) {
return Result::from(
sprintf(
'Migration of %s failed:%s%s%s',
$this->filename,
PHP_EOL,
$t->getMessage(),
PHP_EOL,
),
Result::FAILURE,
);
}
}
}
