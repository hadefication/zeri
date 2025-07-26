<?php

namespace Illuminate\Contracts\Database\Eloquent;

interface SupportsPartialRelations
{








public function ofMany($column = 'id', $aggregate = 'MAX', $relation = null);






public function isOneOfMany();






public function getOneOfManySubQuery();
}
