<?php

namespace Illuminate\Foundation\Http\Middleware;

use Closure;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\Concerns\ExcludesPaths;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Arr;
use Illuminate\Support\InteractsWithTime;
use Symfony\Component\HttpFoundation\Cookie;

class VerifyCsrfToken
{
use InteractsWithTime,
ExcludesPaths;






protected $app;






protected $encrypter;






protected $except = [];






protected static $neverVerify = [];






protected $addHttpCookie = true;







public function __construct(Application $app, Encrypter $encrypter)
{
$this->app = $app;
$this->encrypter = $encrypter;
}










public function handle($request, Closure $next)
{
if (
$this->isReading($request) ||
$this->runningUnitTests() ||
$this->inExceptArray($request) ||
$this->tokensMatch($request)
) {
return tap($next($request), function ($response) use ($request) {
if ($this->shouldAddXsrfTokenCookie()) {
$this->addCookieToResponse($request, $response);
}
});
}

throw new TokenMismatchException('CSRF token mismatch.');
}







protected function isReading($request)
{
return in_array($request->method(), ['HEAD', 'GET', 'OPTIONS']);
}






protected function runningUnitTests()
{
return $this->app->runningInConsole() && $this->app->runningUnitTests();
}






public function getExcludedPaths()
{
return array_merge($this->except, static::$neverVerify);
}







protected function tokensMatch($request)
{
$token = $this->getTokenFromRequest($request);

return is_string($request->session()->token()) &&
is_string($token) &&
hash_equals($request->session()->token(), $token);
}







protected function getTokenFromRequest($request)
{
$token = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');

if (! $token && $header = $request->header('X-XSRF-TOKEN')) {
try {
$token = CookieValuePrefix::remove($this->encrypter->decrypt($header, static::serialized()));
} catch (DecryptException) {
$token = '';
}
}

return $token;
}






public function shouldAddXsrfTokenCookie()
{
return $this->addHttpCookie;
}








protected function addCookieToResponse($request, $response)
{
$config = config('session');

if ($response instanceof Responsable) {
$response = $response->toResponse($request);
}

$response->headers->setCookie($this->newCookie($request, $config));

return $response;
}








protected function newCookie($request, $config)
{
return new Cookie(
'XSRF-TOKEN',
$request->session()->token(),
$this->availableAt(60 * $config['lifetime']),
$config['path'],
$config['domain'],
$config['secure'],
false,
false,
$config['same_site'] ?? null,
$config['partitioned'] ?? false
);
}







public static function except($uris)
{
static::$neverVerify = array_values(array_unique(
array_merge(static::$neverVerify, Arr::wrap($uris))
));
}






public static function serialized()
{
return EncryptCookies::serialized('XSRF-TOKEN');
}






public static function flushState()
{
static::$neverVerify = [];
}
}
