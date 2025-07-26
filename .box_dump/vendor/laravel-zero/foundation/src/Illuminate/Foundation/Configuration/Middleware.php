<?php

namespace Illuminate\Foundation\Configuration;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance;
use Illuminate\Foundation\Http\Middleware\TrimStrings;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\Middleware\TrustHosts;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Middleware
{





protected $global = [];






protected $prepends = [];






protected $appends = [];






protected $removals = [];






protected $replacements = [];






protected $groups = [];






protected $groupPrepends = [];






protected $groupAppends = [];






protected $groupRemovals = [];






protected $groupReplacements = [];






protected $pageMiddleware = [];






protected $trustHosts = false;






protected $statefulApi = false;






protected $apiLimiter;






protected $throttleWithRedis = false;






protected $authenticatedSessions = false;






protected $customAliases = [];






protected $priority = [];






protected $prependPriority = [];






protected $appendPriority = [];







public function prepend(array|string $middleware)
{
$this->prepends = array_merge(
Arr::wrap($middleware),
$this->prepends
);

return $this;
}







public function append(array|string $middleware)
{
$this->appends = array_merge(
$this->appends,
Arr::wrap($middleware)
);

return $this;
}







public function remove(array|string $middleware)
{
$this->removals = array_merge(
$this->removals,
Arr::wrap($middleware)
);

return $this;
}








public function replace(string $search, string $replace)
{
$this->replacements[$search] = $replace;

return $this;
}







public function use(array $middleware)
{
$this->global = $middleware;

return $this;
}








public function group(string $group, array $middleware)
{
$this->groups[$group] = $middleware;

return $this;
}








public function prependToGroup(string $group, array|string $middleware)
{
$this->groupPrepends[$group] = array_merge(
Arr::wrap($middleware),
$this->groupPrepends[$group] ?? []
);

return $this;
}








public function appendToGroup(string $group, array|string $middleware)
{
$this->groupAppends[$group] = array_merge(
$this->groupAppends[$group] ?? [],
Arr::wrap($middleware)
);

return $this;
}








public function removeFromGroup(string $group, array|string $middleware)
{
$this->groupRemovals[$group] = array_merge(
Arr::wrap($middleware),
$this->groupRemovals[$group] ?? []
);

return $this;
}









public function replaceInGroup(string $group, string $search, string $replace)
{
$this->groupReplacements[$group][$search] = $replace;

return $this;
}










public function web(array|string $append = [], array|string $prepend = [], array|string $remove = [], array $replace = [])
{
return $this->modifyGroup('web', $append, $prepend, $remove, $replace);
}










public function api(array|string $append = [], array|string $prepend = [], array|string $remove = [], array $replace = [])
{
return $this->modifyGroup('api', $append, $prepend, $remove, $replace);
}











protected function modifyGroup(string $group, array|string $append, array|string $prepend, array|string $remove, array $replace)
{
if (! empty($append)) {
$this->appendToGroup($group, $append);
}

if (! empty($prepend)) {
$this->prependToGroup($group, $prepend);
}

if (! empty($remove)) {
$this->removeFromGroup($group, $remove);
}

if (! empty($replace)) {
foreach ($replace as $search => $replace) {
$this->replaceInGroup($group, $search, $replace);
}
}

return $this;
}







public function pages(array $middleware)
{
$this->pageMiddleware = $middleware;

return $this;
}







public function alias(array $aliases)
{
$this->customAliases = $aliases;

return $this;
}







public function priority(array $priority)
{
$this->priority = $priority;

return $this;
}








public function prependToPriorityList($before, $prepend)
{
$this->prependPriority[$prepend] = $before;

return $this;
}








public function appendToPriorityList($after, $append)
{
$this->appendPriority[$append] = $after;

return $this;
}






public function getGlobalMiddleware()
{
$middleware = $this->global ?: array_values(array_filter([
\Illuminate\Http\Middleware\ValidatePathEncoding::class,
\Illuminate\Foundation\Http\Middleware\InvokeDeferredCallbacks::class,
$this->trustHosts ? \Illuminate\Http\Middleware\TrustHosts::class : null,
\Illuminate\Http\Middleware\TrustProxies::class,
\Illuminate\Http\Middleware\HandleCors::class,
\Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance::class,
\Illuminate\Http\Middleware\ValidatePostSize::class,
\Illuminate\Foundation\Http\Middleware\TrimStrings::class,
\Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
]));

$middleware = array_map(function ($middleware) {
return $this->replacements[$middleware] ?? $middleware;
}, $middleware);

return array_values(array_filter(
array_diff(
array_unique(array_merge($this->prepends, $middleware, $this->appends)),
$this->removals
)
));
}






public function getMiddlewareGroups()
{
$middleware = [
'web' => array_values(array_filter([
\Illuminate\Cookie\Middleware\EncryptCookies::class,
\Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
\Illuminate\Session\Middleware\StartSession::class,
\Illuminate\View\Middleware\ShareErrorsFromSession::class,
\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
\Illuminate\Routing\Middleware\SubstituteBindings::class,
$this->authenticatedSessions ? 'auth.session' : null,
])),

'api' => array_values(array_filter([
$this->statefulApi ? \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class : null,
$this->apiLimiter ? 'throttle:'.$this->apiLimiter : null,
\Illuminate\Routing\Middleware\SubstituteBindings::class,
])),
];

$middleware = array_merge($middleware, $this->groups);

foreach ($middleware as $group => $groupedMiddleware) {
foreach ($groupedMiddleware as $index => $groupMiddleware) {
if (isset($this->groupReplacements[$group][$groupMiddleware])) {
$middleware[$group][$index] = $this->groupReplacements[$group][$groupMiddleware];
}
}
}

foreach ($this->groupRemovals as $group => $removals) {
$middleware[$group] = array_values(array_filter(
array_diff($middleware[$group] ?? [], $removals)
));
}

foreach ($this->groupPrepends as $group => $prepends) {
$middleware[$group] = array_values(array_filter(
array_unique(array_merge($prepends, $middleware[$group] ?? []))
));
}

foreach ($this->groupAppends as $group => $appends) {
$middleware[$group] = array_values(array_filter(
array_unique(array_merge($middleware[$group] ?? [], $appends))
));
}

return $middleware;
}







public function redirectGuestsTo(callable|string $redirect)
{
return $this->redirectTo(guests: $redirect);
}







public function redirectUsersTo(callable|string $redirect)
{
return $this->redirectTo(users: $redirect);
}








public function redirectTo(callable|string|null $guests = null, callable|string|null $users = null)
{
$guests = is_string($guests) ? fn () => $guests : $guests;
$users = is_string($users) ? fn () => $users : $users;

if ($guests) {
Authenticate::redirectUsing($guests);
AuthenticateSession::redirectUsing($guests);
AuthenticationException::redirectUsing($guests);
}

if ($users) {
RedirectIfAuthenticated::redirectUsing($users);
}

return $this;
}







public function encryptCookies(array $except = [])
{
EncryptCookies::except($except);

return $this;
}







public function validateCsrfTokens(array $except = [])
{
ValidateCsrfToken::except($except);

return $this;
}







public function validateSignatures(array $except = [])
{
ValidateSignature::except($except);

return $this;
}







public function convertEmptyStringsToNull(array $except = [])
{
(new Collection($except))->each(fn (Closure $callback) => ConvertEmptyStringsToNull::skipWhen($callback));

return $this;
}







public function trimStrings(array $except = [])
{
[$skipWhen, $except] = (new Collection($except))->partition(fn ($value) => $value instanceof Closure);

$skipWhen->each(fn (Closure $callback) => TrimStrings::skipWhen($callback));

TrimStrings::except($except->all());

return $this;
}








public function trustHosts(array|callable|null $at = null, bool $subdomains = true)
{
$this->trustHosts = true;

if (! is_null($at)) {
TrustHosts::at($at, $subdomains);
}

return $this;
}








public function trustProxies(array|string|null $at = null, ?int $headers = null)
{
if (! is_null($at)) {
TrustProxies::at($at);
}

if (! is_null($headers)) {
TrustProxies::withHeaders($headers);
}

return $this;
}







public function preventRequestsDuringMaintenance(array $except = [])
{
PreventRequestsDuringMaintenance::except($except);

return $this;
}






public function statefulApi()
{
$this->statefulApi = true;

return $this;
}








public function throttleApi($limiter = 'api', $redis = false)
{
$this->apiLimiter = $limiter;

if ($redis) {
$this->throttleWithRedis();
}

return $this;
}






public function throttleWithRedis()
{
$this->throttleWithRedis = true;

return $this;
}






public function authenticateSessions()
{
$this->authenticatedSessions = true;

return $this;
}






public function getPageMiddleware()
{
return $this->pageMiddleware;
}






public function getMiddlewareAliases()
{
return array_merge($this->defaultAliases(), $this->customAliases);
}






protected function defaultAliases()
{
$aliases = [
'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
'can' => \Illuminate\Auth\Middleware\Authorize::class,
'guest' => \Illuminate\Auth\Middleware\RedirectIfAuthenticated::class,
'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
'throttle' => $this->throttleWithRedis
? \Illuminate\Routing\Middleware\ThrottleRequestsWithRedis::class
: \Illuminate\Routing\Middleware\ThrottleRequests::class,
'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
];

if (class_exists(\Spark\Http\Middleware\VerifyBillableIsSubscribed::class)) {
$aliases['subscribed'] = \Spark\Http\Middleware\VerifyBillableIsSubscribed::class;
}

return $aliases;
}






public function getMiddlewarePriority()
{
return $this->priority;
}






public function getMiddlewarePriorityPrepends()
{
return $this->prependPriority;
}






public function getMiddlewarePriorityAppends()
{
return $this->appendPriority;
}
}
