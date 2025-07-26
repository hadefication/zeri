<?php

namespace Illuminate\Contracts\Database;

class ModelIdentifier
{





public $class;








public $id;






public $relations;






public $connection;






public $collectionClass;









public function __construct($class, $id, array $relations, $connection)
{
$this->id = $id;
$this->class = $class;
$this->relations = $relations;
$this->connection = $connection;
}







public function useCollectionClass(?string $collectionClass)
{
$this->collectionClass = $collectionClass;

return $this;
}
}
