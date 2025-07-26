<?php

namespace Illuminate\Support;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Traits\InteractsWithData;
use League\Uri\QueryString;
use Stringable;

class UriQueryString implements Arrayable, Stringable
{
use InteractsWithData;




public function __construct(protected Uri $uri)
{

}







public function all($keys = null)
{
$query = $this->toArray();

if (! $keys) {
return $query;
}

$results = [];

foreach (is_array($keys) ? $keys : func_get_args() as $key) {
Arr::set($results, $key, Arr::get($query, $key));
}

return $results;
}








protected function data($key = null, $default = null)
{
return $this->get($key, $default);
}




public function get(?string $key = null, mixed $default = null): mixed
{
return data_get($this->toArray(), $key, $default);
}




public function decode(): string
{
return rawurldecode((string) $this);
}




public function value(): string
{
return (string) $this;
}




public function toArray()
{
return QueryString::extract($this->value());
}




public function __toString(): string
{
return (string) $this->uri->getUri()->getQuery();
}
}
