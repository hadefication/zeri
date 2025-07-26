<?php

namespace Illuminate\Testing;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use PHPUnit\Framework\ExpectationFailedException;
use ReflectionProperty;

/**
@mixin


*/
class TestResponseAssert
{



private function __construct(protected TestResponse $response)
{

}




public static function withResponse(TestResponse $response): static
{
return new static($response);
}










public function __call($name, $arguments)
{
try {
Assert::$name(...$arguments);
} catch (ExpectationFailedException $e) {
throw $this->injectResponseContext($e);
}
}










public static function __callStatic($name, $arguments)
{
Assert::$name(...$arguments);
}







protected function injectResponseContext($exception)
{
if ($lastException = $this->response->exceptions->last()) {
return $this->appendExceptionToException($lastException, $exception);
}

if ($this->response->baseResponse instanceof RedirectResponse) {
$session = $this->response->baseResponse->getSession();

if (! is_null($session) && $session->has('errors')) {
return $this->appendErrorsToException($session->get('errors')->all(), $exception);
}
}

if ($this->response->baseResponse->headers->get('Content-Type') === 'application/json') {
$testJson = new AssertableJsonString($this->response->getContent());

if (isset($testJson['errors'])) {
return $this->appendErrorsToException($testJson->json(), $exception, true);
}
}

return $exception;
}








protected function appendExceptionToException($exceptionToAppend, $exception)
{
$exceptionMessage = is_string($exceptionToAppend) ? $exceptionToAppend : $exceptionToAppend->getMessage();

$exceptionToAppend = (string) $exceptionToAppend;

$message = <<<"EOF"
            The following exception occurred during the last request:

            $exceptionToAppend

            ----------------------------------------------------------------------------------

            $exceptionMessage
            EOF;

return $this->appendMessageToException($message, $exception);
}









protected function appendErrorsToException($errors, $exception, $json = false)
{
$errors = $json
? json_encode($errors, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
: implode(PHP_EOL, Arr::flatten($errors));


if (str_contains($exception->getMessage(), $errors)) {
return $exception;
}

$message = <<<"EOF"
            The following errors occurred during the last request:

            $errors
            EOF;

return $this->appendMessageToException($message, $exception);
}








protected function appendMessageToException($message, $exception)
{
$property = new ReflectionProperty($exception, 'message');

$property->setValue(
$exception,
$exception->getMessage().PHP_EOL.PHP_EOL.$message.PHP_EOL
);

return $exception;
}
}
