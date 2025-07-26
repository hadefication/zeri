<?php

namespace Illuminate\Testing\Constraints;

use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Connection;
use PHPUnit\Framework\Constraint\Constraint;

class HasInDatabase extends Constraint
{





protected $show = 3;






protected $database;






protected $data;







public function __construct(Connection $database, array $data)
{
$this->data = $data;

$this->database = $database;
}







public function matches($table): bool
{
return $this->database->table($table)
->where($this->data)
->exists();
}







public function failureDescription($table): string
{
return sprintf(
"a row in the table [%s] matches the attributes %s.\n\n%s",
$table, $this->toString(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), $this->getAdditionalInfo($table)
);
}







protected function getAdditionalInfo($table)
{
$query = $this->database->table($table);

$similarResults = $query->where(
array_key_first($this->data),
$this->data[array_key_first($this->data)]
)->select(array_keys($this->data))->limit($this->show)->get();

if ($similarResults->isNotEmpty()) {
$description = 'Found similar results: '.json_encode($similarResults, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else {
$query = $this->database->table($table);

$results = $query->select(array_keys($this->data))->limit($this->show)->get();

if ($results->isEmpty()) {
return 'The table is empty';
}

$description = 'Found: '.json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

if ($query->count() > $this->show) {
$description .= sprintf(' and %s others', $query->count() - $this->show);
}

return $description;
}







public function toString($options = 0): string
{
foreach ($this->data as $key => $data) {
$output[$key] = $data instanceof Expression ? $data->getValue($this->database->getQueryGrammar()) : $data;
}

return json_encode($output ?? [], $options);
}
}
