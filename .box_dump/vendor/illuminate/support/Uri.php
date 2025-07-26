<?php

namespace Illuminate\Support;

use Closure;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Dumpable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;
use JsonSerializable;
use League\Uri\Contracts\UriInterface;
use League\Uri\Uri as LeagueUri;
use SensitiveParameter;
use Stringable;

class Uri implements Htmlable, JsonSerializable, Responsable, Stringable
{
use Conditionable, Dumpable, Macroable, Tappable;




protected UriInterface $uri;




protected static ?Closure $urlGeneratorResolver = null;




public function __construct(UriInterface|Stringable|string $uri = '')
{
$this->uri = $uri instanceof UriInterface ? $uri : LeagueUri::new((string) $uri);
}




public static function of(UriInterface|Stringable|string $uri = ''): static
{
return new static($uri);
}




public static function to(string $path): static
{
return new static(call_user_func(static::$urlGeneratorResolver)->to($path));
}











public static function route($name, $parameters = [], $absolute = true): static
{
return new static(call_user_func(static::$urlGeneratorResolver)->route($name, $parameters, $absolute));
}












public static function signedRoute($name, $parameters = [], $expiration = null, $absolute = true): static
{
return new static(call_user_func(static::$urlGeneratorResolver)->signedRoute($name, $parameters, $expiration, $absolute));
}










public static function temporarySignedRoute($name, $expiration, $parameters = [], $absolute = true): static
{
return static::signedRoute($name, $parameters, $expiration, $absolute);
}











public static function action($action, $parameters = [], $absolute = true): static
{
return new static(call_user_func(static::$urlGeneratorResolver)->action($action, $parameters, $absolute));
}




public function scheme(): ?string
{
return $this->uri->getScheme();
}




public function user(bool $withPassword = false): ?string
{
return $withPassword
? $this->uri->getUserInfo()
: $this->uri->getUsername();
}




public function password(): ?string
{
return $this->uri->getPassword();
}




public function host(): ?string
{
return $this->uri->getHost();
}




public function port(): ?int
{
return $this->uri->getPort();
}






public function path(): ?string
{
$path = trim((string) $this->uri->getPath(), '/');

return $path === '' ? '/' : $path;
}






public function pathSegments(): Collection
{
$path = $this->path();

return $path === '/' ? new Collection : new Collection(explode('/', $path));
}




public function query(): UriQueryString
{
return new UriQueryString($this);
}




public function fragment(): ?string
{
return $this->uri->getFragment();
}




public function withScheme(Stringable|string $scheme): static
{
return new static($this->uri->withScheme($scheme));
}




public function withUser(Stringable|string|null $user, #[SensitiveParameter] Stringable|string|null $password = null): static
{
return new static($this->uri->withUserInfo($user, $password));
}




public function withHost(Stringable|string $host): static
{
return new static($this->uri->withHost($host));
}




public function withPort(?int $port): static
{
return new static($this->uri->withPort($port));
}




public function withPath(Stringable|string $path): static
{
return new static($this->uri->withPath(Str::start((string) $path, '/')));
}




public function withQuery(array $query, bool $merge = true): static
{
foreach ($query as $key => $value) {
if ($value instanceof UrlRoutable) {
$query[$key] = $value->getRouteKey();
}
}

if ($merge) {
$mergedQuery = $this->query()->all();

foreach ($query as $key => $value) {
data_set($mergedQuery, $key, $value);
}

$newQuery = $mergedQuery;
} else {
$newQuery = [];

foreach ($query as $key => $value) {
data_set($newQuery, $key, $value);
}
}

return new static($this->uri->withQuery(Arr::query($newQuery) ?: null));
}




public function withQueryIfMissing(array $query): static
{
$currentQuery = $this->query();

foreach ($query as $key => $value) {
if (! $currentQuery->missing($key)) {
Arr::forget($query, $key);
}
}

return $this->withQuery($query);
}




public function pushOntoQuery(string $key, mixed $value): static
{
$currentValue = data_get($this->query()->all(), $key);

$values = Arr::wrap($value);

return $this->withQuery([$key => match (true) {
is_array($currentValue) && array_is_list($currentValue) => array_values(array_unique([...$currentValue, ...$values])),
is_array($currentValue) => [...$currentValue, ...$values],
! is_null($currentValue) => [$currentValue, ...$values],
default => $values,
}]);
}




public function withoutQuery(array|string $keys): static
{
return $this->replaceQuery(Arr::except($this->query()->all(), $keys));
}




public function replaceQuery(array $query): static
{
return $this->withQuery($query, merge: false);
}




public function withFragment(string $fragment): static
{
return new static($this->uri->withFragment($fragment));
}




public function redirect(int $status = 302, array $headers = []): RedirectResponse
{
return new RedirectResponse($this->value(), $status, $headers);
}






public function toStringable()
{
return Str::of($this->value());
}







public function toResponse($request)
{
return new RedirectResponse($this->value());
}






public function toHtml()
{
return $this->value();
}




public function decode(): string
{
if (empty($this->query()->toArray())) {
return $this->value();
}

return Str::replace(Str::after($this->value(), '?'), $this->query()->decode(), $this->value());
}




public function value(): string
{
return (string) $this;
}




public function isEmpty(): bool
{
return trim($this->value()) === '';
}







public function dump(...$args)
{
dump($this->value(), ...$args);

return $this;
}




public static function setUrlGeneratorResolver(Closure $urlGeneratorResolver): void
{
static::$urlGeneratorResolver = $urlGeneratorResolver;
}




public function getUri(): UriInterface
{
return $this->uri;
}






public function jsonSerialize(): string
{
return $this->value();
}




public function __toString(): string
{
return $this->uri->toString();
}
}
