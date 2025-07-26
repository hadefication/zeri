<?php

namespace Illuminate\Testing\Constraints;

use Illuminate\Database\Connection;
use PHPUnit\Framework\Constraint\Constraint;
use ReflectionClass;

class CountInDatabase extends Constraint
{





protected $database;






protected $expectedCount;






protected $actualCount;







public function __construct(Connection $database, int $expectedCount)
{
$this->expectedCount = $expectedCount;

$this->database = $database;
}







public function matches($table): bool
{
$this->actualCount = $this->database->table($table)->count();

return $this->actualCount === $this->expectedCount;
}







public function failureDescription($table): string
{
return sprintf(
"table [%s] matches expected entries count of %s. Entries found: %s.\n",
$table, $this->expectedCount, $this->actualCount
);
}







public function toString($options = 0): string
{
return (new ReflectionClass($this))->name;
}
}
