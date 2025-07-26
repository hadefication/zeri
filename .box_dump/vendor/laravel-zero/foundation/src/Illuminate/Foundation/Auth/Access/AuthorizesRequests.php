<?php

namespace Illuminate\Foundation\Auth\Access;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Support\Str;

use function Illuminate\Support\enum_value;

trait AuthorizesRequests
{









public function authorize($ability, $arguments = [])
{
[$ability, $arguments] = $this->parseAbilityAndArguments($ability, $arguments);

return app(Gate::class)->authorize($ability, $arguments);
}











public function authorizeForUser($user, $ability, $arguments = [])
{
[$ability, $arguments] = $this->parseAbilityAndArguments($ability, $arguments);

return app(Gate::class)->forUser($user)->authorize($ability, $arguments);
}








protected function parseAbilityAndArguments($ability, $arguments)
{
$ability = enum_value($ability);

if (is_string($ability) && ! str_contains($ability, '\\')) {
return [$ability, $arguments];
}

$method = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2]['function'];

return [$this->normalizeGuessedAbilityName($method), $ability];
}







protected function normalizeGuessedAbilityName($ability)
{
$map = $this->resourceAbilityMap();

return $map[$ability] ?? $ability;
}










public function authorizeResource($model, $parameter = null, array $options = [], $request = null)
{
$model = is_array($model) ? implode(',', $model) : $model;

$parameter = is_array($parameter) ? implode(',', $parameter) : $parameter;

$parameter = $parameter ?: Str::snake(class_basename($model));

$middleware = [];

foreach ($this->resourceAbilityMap() as $method => $ability) {
$modelName = in_array($method, $this->resourceMethodsWithoutModels()) ? $model : $parameter;

$middleware["can:{$ability},{$modelName}"][] = $method;
}

foreach ($middleware as $middlewareName => $methods) {
$this->middleware($middlewareName, $options)->only($methods);
}
}






protected function resourceAbilityMap()
{
return [
'index' => 'viewAny',
'show' => 'view',
'create' => 'create',
'store' => 'create',
'edit' => 'update',
'update' => 'update',
'destroy' => 'delete',
];
}






protected function resourceMethodsWithoutModels()
{
return ['index', 'create', 'store'];
}
}
