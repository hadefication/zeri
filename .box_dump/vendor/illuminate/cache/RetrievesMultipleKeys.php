<?php

namespace Illuminate\Cache;

use Illuminate\Support\Collection;

trait RetrievesMultipleKeys
{








public function many(array $keys)
{
$return = [];

$keys = (new Collection($keys))
->mapWithKeys(fn ($value, $key) => [is_string($key) ? $key : $value => is_string($key) ? $value : null])
->all();

foreach ($keys as $key => $default) {
/**
@phpstan-ignore */
$return[$key] = $this->get($key, $default);
}

return $return;
}








public function putMany(array $values, $seconds)
{
$manyResult = null;

foreach ($values as $key => $value) {
$result = $this->put($key, $value, $seconds);

$manyResult = is_null($manyResult) ? $result : $result && $manyResult;
}

return $manyResult ?: false;
}
}
