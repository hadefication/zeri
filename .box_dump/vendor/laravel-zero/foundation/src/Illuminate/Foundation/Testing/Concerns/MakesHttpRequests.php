<?php

namespace Illuminate\Foundation\Testing\Concerns;

use BackedEnum;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Uri;
use Illuminate\Testing\LoggedExceptionCollection;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

trait MakesHttpRequests
{





protected $defaultHeaders = [];






protected $defaultCookies = [];






protected $unencryptedCookies = [];






protected $serverVariables = [];






protected $followRedirects = false;






protected $encryptCookies = true;








protected $withCredentials = false;







public function withHeaders(array $headers)
{
$this->defaultHeaders = array_merge($this->defaultHeaders, $headers);

return $this;
}








public function withHeader(string $name, string $value)
{
$this->defaultHeaders[$name] = $value;

return $this;
}







public function withoutHeader(string $name)
{
unset($this->defaultHeaders[$name]);

return $this;
}







public function withoutHeaders(array $headers)
{
foreach ($headers as $name) {
$this->withoutHeader($name);
}

return $this;
}








public function withToken(string $token, string $type = 'Bearer')
{
return $this->withHeader('Authorization', $type.' '.$token);
}








public function withBasicAuth(string $username, string $password)
{
return $this->withToken(base64_encode("$username:$password"), 'Basic');
}






public function withoutToken()
{
return $this->withoutHeader('Authorization');
}






public function flushHeaders()
{
$this->defaultHeaders = [];

return $this;
}







public function withServerVariables(array $server)
{
$this->serverVariables = $server;

return $this;
}







public function withoutMiddleware($middleware = null)
{
if (is_null($middleware)) {
$this->app->instance('middleware.disable', true);

return $this;
}

foreach ((array) $middleware as $abstract) {
$this->app->instance($abstract, new class
{
public function handle($request, $next)
{
return $next($request);
}
});
}

return $this;
}







public function withMiddleware($middleware = null)
{
if (is_null($middleware)) {
unset($this->app['middleware.disable']);

return $this;
}

foreach ((array) $middleware as $abstract) {
unset($this->app[$abstract]);
}

return $this;
}







public function withCookies(array $cookies)
{
$this->defaultCookies = array_merge($this->defaultCookies, $cookies);

return $this;
}








public function withCookie(string $name, string $value)
{
$this->defaultCookies[$name] = $value;

return $this;
}







public function withUnencryptedCookies(array $cookies)
{
$this->unencryptedCookies = array_merge($this->unencryptedCookies, $cookies);

return $this;
}








public function withUnencryptedCookie(string $name, string $value)
{
$this->unencryptedCookies[$name] = $value;

return $this;
}






public function followingRedirects()
{
$this->followRedirects = true;

return $this;
}






public function withCredentials()
{
$this->withCredentials = true;

return $this;
}






public function disableCookieEncryption()
{
$this->encryptCookies = false;

return $this;
}







public function from(string $url)
{
$this->app['session']->setPreviousUrl($url);

return $this->withHeader('referer', $url);
}








public function fromRoute(BackedEnum|string $name, $parameters = [])
{
return $this->from($this->app['url']->route($name, $parameters));
}






public function withPrecognition()
{
return $this->withHeader('Precognition', 'true');
}








public function get($uri, array $headers = [])
{
$server = $this->transformHeadersToServerVars($headers);
$cookies = $this->prepareCookiesForRequest();

return $this->call('GET', $uri, [], $cookies, [], $server);
}









public function getJson($uri, array $headers = [], $options = 0)
{
return $this->json('GET', $uri, [], $headers, $options);
}









public function post($uri, array $data = [], array $headers = [])
{
$server = $this->transformHeadersToServerVars($headers);
$cookies = $this->prepareCookiesForRequest();

return $this->call('POST', $uri, $data, $cookies, [], $server);
}










public function postJson($uri, array $data = [], array $headers = [], $options = 0)
{
return $this->json('POST', $uri, $data, $headers, $options);
}









public function put($uri, array $data = [], array $headers = [])
{
$server = $this->transformHeadersToServerVars($headers);
$cookies = $this->prepareCookiesForRequest();

return $this->call('PUT', $uri, $data, $cookies, [], $server);
}










public function putJson($uri, array $data = [], array $headers = [], $options = 0)
{
return $this->json('PUT', $uri, $data, $headers, $options);
}









public function patch($uri, array $data = [], array $headers = [])
{
$server = $this->transformHeadersToServerVars($headers);
$cookies = $this->prepareCookiesForRequest();

return $this->call('PATCH', $uri, $data, $cookies, [], $server);
}










public function patchJson($uri, array $data = [], array $headers = [], $options = 0)
{
return $this->json('PATCH', $uri, $data, $headers, $options);
}









public function delete($uri, array $data = [], array $headers = [])
{
$server = $this->transformHeadersToServerVars($headers);
$cookies = $this->prepareCookiesForRequest();

return $this->call('DELETE', $uri, $data, $cookies, [], $server);
}










public function deleteJson($uri, array $data = [], array $headers = [], $options = 0)
{
return $this->json('DELETE', $uri, $data, $headers, $options);
}









public function options($uri, array $data = [], array $headers = [])
{
$server = $this->transformHeadersToServerVars($headers);

$cookies = $this->prepareCookiesForRequest();

return $this->call('OPTIONS', $uri, $data, $cookies, [], $server);
}










public function optionsJson($uri, array $data = [], array $headers = [], $options = 0)
{
return $this->json('OPTIONS', $uri, $data, $headers, $options);
}








public function head($uri, array $headers = [])
{
$server = $this->transformHeadersToServerVars($headers);

$cookies = $this->prepareCookiesForRequest();

return $this->call('HEAD', $uri, [], $cookies, [], $server);
}











public function json($method, $uri, array $data = [], array $headers = [], $options = 0)
{
$files = $this->extractFilesFromDataArray($data);

$content = json_encode($data, $options);

$headers = array_merge([
'CONTENT_LENGTH' => mb_strlen($content, '8bit'),
'CONTENT_TYPE' => 'application/json',
'Accept' => 'application/json',
], $headers);

return $this->call(
$method,
$uri,
[],
$this->prepareCookiesForJsonRequest(),
$files,
$this->transformHeadersToServerVars($headers),
$content
);
}













public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
{
$kernel = $this->app->make(HttpKernel::class);

$files = array_merge($files, $this->extractFilesFromDataArray($parameters));

$symfonyRequest = SymfonyRequest::create(
$this->prepareUrlForRequest($uri), $method, $parameters,
$cookies, $files, array_replace($this->serverVariables, $server), $content
);

$response = $kernel->handle(
$request = $this->createTestRequest($symfonyRequest)
);

$kernel->terminate($request, $response);

if ($this->followRedirects) {
$response = $this->followRedirects($response);
}

return $this->createTestResponse($response, $request);
}







protected function prepareUrlForRequest($uri)
{
$uri = $uri instanceof Uri ? $uri->value() : $uri;

if (str_starts_with($uri, '/')) {
$uri = substr($uri, 1);
}

return trim(url($uri), '/');
}







protected function transformHeadersToServerVars(array $headers)
{
return (new Collection(array_merge($this->defaultHeaders, $headers)))->mapWithKeys(function ($value, $name) {
$name = strtr(strtoupper($name), '-', '_');

return [$this->formatServerHeaderKey($name) => $value];
})->all();
}







protected function formatServerHeaderKey($name)
{
if (! str_starts_with($name, 'HTTP_') && $name !== 'CONTENT_TYPE' && $name !== 'REMOTE_ADDR') {
return 'HTTP_'.$name;
}

return $name;
}







protected function extractFilesFromDataArray(&$data)
{
$files = [];

foreach ($data as $key => $value) {
if ($value instanceof SymfonyUploadedFile) {
$files[$key] = $value;

unset($data[$key]);
}

if (is_array($value)) {
$files[$key] = $this->extractFilesFromDataArray($value);

$data[$key] = $value;
}
}

return $files;
}






protected function prepareCookiesForRequest()
{
if (! $this->encryptCookies) {
return array_merge($this->defaultCookies, $this->unencryptedCookies);
}

return (new Collection($this->defaultCookies))->map(function ($value, $key) {
return encrypt(CookieValuePrefix::create($key, app('encrypter')->getKey()).$value, false);
})->merge($this->unencryptedCookies)->all();
}






protected function prepareCookiesForJsonRequest()
{
return $this->withCredentials ? $this->prepareCookiesForRequest() : [];
}







protected function followRedirects($response)
{
$this->followRedirects = false;

while ($response->isRedirect()) {
$response = $this->get($response->headers->get('Location'));
}

return $response;
}







protected function createTestRequest($symfonyRequest)
{
return Request::createFromBase($symfonyRequest);
}








protected function createTestResponse($response, $request)
{
return tap(TestResponse::fromBaseResponse($response, $request), function ($response) {
$response->withExceptions(
$this->app->bound(LoggedExceptionCollection::class)
? $this->app->make(LoggedExceptionCollection::class)
: new LoggedExceptionCollection
);
});
}
}
