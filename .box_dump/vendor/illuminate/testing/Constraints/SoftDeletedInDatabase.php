<?php

namespace Illuminate\Testing\Constraints;

use Illuminate\Database\Connection;
use PHPUnit\Framework\Constraint\Constraint;

class SoftDeletedInDatabase extends Constraint
{





protected $show = 3;






protected $database;






protected $data;






protected $deletedAtColumn;








public function __construct(Connection $database, array $data, string $deletedAtColumn)
{
$this->data = $data;

$this->database = $database;

$this->deletedAtColumn = $deletedAtColumn;
}







public function matches($table): bool
{
return $this->database->table($table)
->where($this->data)
->whereNotNull($this->deletedAtColumn)
->exists();
}







public function failureDescription($table): string
{
return sprintf(
"any soft deleted row in the table [%s] matches the attributes %s.\n\n%s",
$table, $this->toString(), $this->getAdditionalInfo($table)
);
}







protected function getAdditionalInfo($table)
{
$query = $this->database->table($table);

$results = $query->limit($this->show)->get();

if ($results->isEmpty()) {
return 'The table is empty';
}

$description = 'Found: '.json_encode($results, JSON_PRETTY_PRINT);

if ($query->count() > $this->show) {
$description .= sprintf(' and %s others', $query->count() - $this->show);
}

return $description;
}






public function toString(): string
{
return json_encode($this->data);
}
}
