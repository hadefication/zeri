<?php

namespace Illuminate\Foundation\Exceptions;

use Closure;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Cache\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Cache\RateLimiting\Unlimited;
use Illuminate\Console\View\Components\BulletList;
use Illuminate\Console\View\Components\Error;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Illuminate\Contracts\Debug\ShouldntReport;
use Illuminate\Contracts\Foundation\ExceptionRenderer;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\MultipleRecordsFoundException;
use Illuminate\Database\RecordNotFoundException;
use Illuminate\Database\RecordsNotFoundException;
use Illuminate\Foundation\Exceptions\Renderer\Renderer;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Exceptions\BackedEnumCaseNotFoundException;
use Illuminate\Routing\Router;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Lottery;
use Illuminate\Support\Reflector;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\ReflectsClosures;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;
use Symfony\Component\HttpFoundation\Exception\RequestExceptionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use WeakMap;

class Handler implements ExceptionHandlerContract
{
use ReflectsClosures;






protected $container;






protected $dontReport = [];






protected $reportCallbacks = [];






protected $levels = [];






protected $throttleCallbacks = [];






protected $contextCallbacks = [];






protected $renderCallbacks = [];






protected $shouldRenderJsonWhenCallback;






protected $finalizeResponseCallback;






protected $exceptionMap = [];






protected $hashThrottleKeys = true;






protected $internalDontReport = [
AuthenticationException::class,
AuthorizationException::class,
BackedEnumCaseNotFoundException::class,
HttpException::class,
HttpResponseException::class,
ModelNotFoundException::class,
MultipleRecordsFoundException::class,
RecordNotFoundException::class,
RecordsNotFoundException::class,
RequestExceptionInterface::class,
TokenMismatchException::class,
ValidationException::class,
];






protected $dontFlash = [
'current_password',
'password',
'password_confirmation',
];






protected $withoutDuplicates = false;






protected $reportedExceptionMap;






public function __construct(Container $container)
{
$this->container = $container;

$this->reportedExceptionMap = new WeakMap;

$this->register();
}






public function register()
{

}







public function reportable(callable $reportUsing)
{
if (! $reportUsing instanceof Closure) {
$reportUsing = Closure::fromCallable($reportUsing);
}

return tap(new ReportableHandler($reportUsing), function ($callback) {
$this->reportCallbacks[] = $callback;
});
}







public function renderable(callable $renderUsing)
{
if (! $renderUsing instanceof Closure) {
$renderUsing = Closure::fromCallable($renderUsing);
}

$this->renderCallbacks[] = $renderUsing;

return $this;
}










public function map($from, $to = null)
{
if (is_string($to)) {
$to = fn ($exception) => new $to('', 0, $exception);
}

if (is_callable($from) && is_null($to)) {
$from = $this->firstClosureParameterType($to = $from);
}

if (! is_string($from) || ! $to instanceof Closure) {
throw new InvalidArgumentException('Invalid exception mapping.');
}

$this->exceptionMap[$from] = $to;

return $this;
}









public function dontReport(array|string $exceptions)
{
return $this->ignore($exceptions);
}







public function ignore(array|string $exceptions)
{
$exceptions = Arr::wrap($exceptions);

$this->dontReport = array_values(array_unique(array_merge($this->dontReport, $exceptions)));

return $this;
}







public function dontFlash(array|string $attributes)
{
$this->dontFlash = array_values(array_unique(
array_merge($this->dontFlash, Arr::wrap($attributes))
));

return $this;
}








public function level($type, $level)
{
$this->levels[$type] = $level;

return $this;
}









public function report(Throwable $e)
{
$e = $this->mapException($e);

if ($this->shouldntReport($e)) {
return;
}

$this->reportThrowable($e);
}









protected function reportThrowable(Throwable $e): void
{
$this->reportedExceptionMap[$e] = true;

if (Reflector::isCallable($reportCallable = [$e, 'report']) &&
$this->container->call($reportCallable) !== false) {
return;
}

foreach ($this->reportCallbacks as $reportCallback) {
if ($reportCallback->handles($e) && $reportCallback($e) === false) {
return;
}
}

try {
$logger = $this->newLogger();
} catch (Exception) {
throw $e;
}

$level = $this->mapLogLevel($e);

$context = $this->buildExceptionContext($e);

method_exists($logger, $level)
? $logger->{$level}($e->getMessage(), $context)
: $logger->log($level, $e->getMessage(), $context);
}







public function shouldReport(Throwable $e)
{
return ! $this->shouldntReport($e);
}







protected function shouldntReport(Throwable $e)
{
if ($this->withoutDuplicates && ($this->reportedExceptionMap[$e] ?? false)) {
return true;
}

if ($e instanceof ShouldntReport) {
return true;
}

$dontReport = array_merge($this->dontReport, $this->internalDontReport);

if (! is_null(Arr::first($dontReport, fn ($type) => $e instanceof $type))) {
return true;
}

return rescue(fn () => with($this->throttle($e), function ($throttle) use ($e) {
if ($throttle instanceof Unlimited || $throttle === null) {
return false;
}

if ($throttle instanceof Lottery) {
return ! $throttle($e);
}

return ! $this->container->make(RateLimiter::class)->attempt(
with($throttle->key ?: 'illuminate:foundation:exceptions:'.$e::class, fn ($key) => $this->hashThrottleKeys ? hash('xxh128', $key) : $key),
$throttle->maxAttempts,
fn () => true,
$throttle->decaySeconds
);
}), rescue: false, report: false);
}







protected function throttle(Throwable $e)
{
foreach ($this->throttleCallbacks as $throttleCallback) {
foreach ($this->firstClosureParameterTypes($throttleCallback) as $type) {
if (is_a($e, $type)) {
$response = $throttleCallback($e);

if (! is_null($response)) {
return $response;
}
}
}
}

return Limit::none();
}







public function throttleUsing(callable $throttleUsing)
{
if (! $throttleUsing instanceof Closure) {
$throttleUsing = Closure::fromCallable($throttleUsing);
}

$this->throttleCallbacks[] = $throttleUsing;

return $this;
}







public function stopIgnoring(array|string $exceptions)
{
$exceptions = Arr::wrap($exceptions);

$this->dontReport = (new Collection($this->dontReport))
->reject(fn ($ignored) => in_array($ignored, $exceptions))
->values()
->all();

$this->internalDontReport = (new Collection($this->internalDontReport))
->reject(fn ($ignored) => in_array($ignored, $exceptions))
->values()
->all();

return $this;
}







protected function buildExceptionContext(Throwable $e)
{
return array_merge(
$this->exceptionContext($e),
$this->context(),
['exception' => $e]
);
}







protected function exceptionContext(Throwable $e)
{
$context = [];

if (method_exists($e, 'context')) {
$context = $e->context();
}

foreach ($this->contextCallbacks as $callback) {
$context = array_merge($context, $callback($e, $context));
}

return $context;
}






protected function context()
{
try {
return array_filter([
'userId' => Auth::id(),
]);
} catch (Throwable) {
return [];
}
}







public function buildContextUsing(Closure $contextCallback)
{
$this->contextCallbacks[] = $contextCallback;

return $this;
}










public function render($request, Throwable $e)
{
$e = $this->mapException($e);

if (method_exists($e, 'render') && $response = $e->render($request)) {
return $this->finalizeRenderedResponse(
$request,
Router::toResponse($request, $response),
$e
);
}

if ($e instanceof Responsable) {
return $this->finalizeRenderedResponse($request, $e->toResponse($request), $e);
}

$e = $this->prepareException($e);

if ($response = $this->renderViaCallbacks($request, $e)) {
return $this->finalizeRenderedResponse($request, $response, $e);
}

return $this->finalizeRenderedResponse($request, match (true) {
$e instanceof HttpResponseException => $e->getResponse(),
$e instanceof AuthenticationException => $this->unauthenticated($request, $e),
$e instanceof ValidationException => $this->convertValidationExceptionToResponse($e, $request),
default => $this->renderExceptionResponse($request, $e),
}, $e);
}









protected function finalizeRenderedResponse($request, $response, Throwable $e)
{
return $this->finalizeResponseCallback
? call_user_func($this->finalizeResponseCallback, $response, $e, $request)
: $response;
}







public function respondUsing($callback)
{
$this->finalizeResponseCallback = $callback;

return $this;
}







protected function prepareException(Throwable $e)
{
return match (true) {
$e instanceof BackedEnumCaseNotFoundException => new NotFoundHttpException($e->getMessage(), $e),
$e instanceof ModelNotFoundException => new NotFoundHttpException($e->getMessage(), $e),
$e instanceof AuthorizationException && $e->hasStatus() => new HttpException(
$e->status(), $e->response()?->message() ?: (Response::$statusTexts[$e->status()] ?? 'Whoops, looks like something went wrong.'), $e
),
$e instanceof AuthorizationException && ! $e->hasStatus() => new AccessDeniedHttpException($e->getMessage(), $e),
$e instanceof TokenMismatchException => new HttpException(419, $e->getMessage(), $e),
$e instanceof RequestExceptionInterface => new BadRequestHttpException('Bad request.', $e),
$e instanceof RecordNotFoundException => new NotFoundHttpException('Not found.', $e),
$e instanceof RecordsNotFoundException => new NotFoundHttpException('Not found.', $e),
default => $e,
};
}







protected function mapException(Throwable $e)
{
if (method_exists($e, 'getInnerException') &&
($inner = $e->getInnerException()) instanceof Throwable) {
return $inner;
}

foreach ($this->exceptionMap as $class => $mapper) {
if (is_a($e, $class)) {
return $mapper($e);
}
}

return $e;
}










protected function renderViaCallbacks($request, Throwable $e)
{
foreach ($this->renderCallbacks as $renderCallback) {
foreach ($this->firstClosureParameterTypes($renderCallback) as $type) {
if (is_a($e, $type)) {
$response = $renderCallback($e, $request);

if (! is_null($response)) {
return $response;
}
}
}
}
}








protected function renderExceptionResponse($request, Throwable $e)
{
return $this->shouldReturnJson($request, $e)
? $this->prepareJsonResponse($request, $e)
: $this->prepareResponse($request, $e);
}








protected function unauthenticated($request, AuthenticationException $exception)
{
return $this->shouldReturnJson($request, $exception)
? response()->json(['message' => $exception->getMessage()], 401)
: redirect()->guest($exception->redirectTo($request) ?? route('login'));
}








protected function convertValidationExceptionToResponse(ValidationException $e, $request)
{
if ($e->response) {
return $e->response;
}

return $this->shouldReturnJson($request, $e)
? $this->invalidJson($request, $e)
: $this->invalid($request, $e);
}








protected function invalid($request, ValidationException $exception)
{
return redirect($exception->redirectTo ?? url()->previous())
->withInput(Arr::except($request->input(), $this->dontFlash))
->withErrors($exception->errors(), $request->input('_error_bag', $exception->errorBag));
}








protected function invalidJson($request, ValidationException $exception)
{
return response()->json([
'message' => $exception->getMessage(),
'errors' => $exception->errors(),
], $exception->status);
}








protected function shouldReturnJson($request, Throwable $e)
{
return $this->shouldRenderJsonWhenCallback
? call_user_func($this->shouldRenderJsonWhenCallback, $request, $e)
: $request->expectsJson();
}







public function shouldRenderJsonWhen($callback)
{
$this->shouldRenderJsonWhenCallback = $callback;

return $this;
}








protected function prepareResponse($request, Throwable $e)
{
if (! $this->isHttpException($e) && config('app.debug')) {
return $this->toIlluminateResponse($this->convertExceptionToResponse($e), $e)->prepare($request);
}

if (! $this->isHttpException($e)) {
$e = new HttpException(500, $e->getMessage(), $e);
}

return $this->toIlluminateResponse(
$this->renderHttpException($e), $e
)->prepare($request);
}







protected function convertExceptionToResponse(Throwable $e)
{
return new SymfonyResponse(
$this->renderExceptionContent($e),
$this->isHttpException($e) ? $e->getStatusCode() : 500,
$this->isHttpException($e) ? $e->getHeaders() : []
);
}







protected function renderExceptionContent(Throwable $e)
{
try {
if (config('app.debug')) {
if (app()->has(ExceptionRenderer::class)) {
return $this->renderExceptionWithCustomRenderer($e);
} elseif ($this->container->bound(Renderer::class)) {
return $this->container->make(Renderer::class)->render(request(), $e);
}
}

return $this->renderExceptionWithSymfony($e, config('app.debug'));
} catch (Throwable $e) {
return $this->renderExceptionWithSymfony($e, config('app.debug'));
}
}







protected function renderExceptionWithCustomRenderer(Throwable $e)
{
return app(ExceptionRenderer::class)->render($e);
}








protected function renderExceptionWithSymfony(Throwable $e, $debug)
{
$renderer = new HtmlErrorRenderer($debug);

return $renderer->render($e)->getAsString();
}







protected function renderHttpException(HttpExceptionInterface $e)
{
$this->registerErrorViewPaths();

if ($view = $this->getHttpExceptionView($e)) {
try {
return response()->view($view, [
'errors' => new ViewErrorBag,
'exception' => $e,
], $e->getStatusCode(), $e->getHeaders());
} catch (Throwable $t) {
config('app.debug') && throw $t;

$this->report($t);
}
}

return $this->convertExceptionToResponse($e);
}






protected function registerErrorViewPaths()
{
(new RegisterErrorViewPaths)();
}







protected function getHttpExceptionView(HttpExceptionInterface $e)
{
$view = 'errors::'.$e->getStatusCode();

if (view()->exists($view)) {
return $view;
}

$view = substr($view, 0, -2).'xx';

if (view()->exists($view)) {
return $view;
}

return null;
}








protected function toIlluminateResponse($response, Throwable $e)
{
if ($response instanceof SymfonyRedirectResponse) {
$response = new RedirectResponse(
$response->getTargetUrl(), $response->getStatusCode(), $response->headers->all()
);
} else {
$response = new Response(
$response->getContent(), $response->getStatusCode(), $response->headers->all()
);
}

return $response->withException($e);
}








protected function prepareJsonResponse($request, Throwable $e)
{
return new JsonResponse(
$this->convertExceptionToArray($e),
$this->isHttpException($e) ? $e->getStatusCode() : 500,
$this->isHttpException($e) ? $e->getHeaders() : [],
JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
);
}







protected function convertExceptionToArray(Throwable $e)
{
return config('app.debug') ? [
'message' => $e->getMessage(),
'exception' => get_class($e),
'file' => $e->getFile(),
'line' => $e->getLine(),
'trace' => (new Collection($e->getTrace()))->map(fn ($trace) => Arr::except($trace, ['args']))->all(),
] : [
'message' => $this->isHttpException($e) ? $e->getMessage() : 'Server Error',
];
}










public function renderForConsole($output, Throwable $e)
{
if ($e instanceof CommandNotFoundException) {
$message = Str::of($e->getMessage())->explode('.')->first();

if (! empty($alternatives = $e->getAlternatives())) {
$message .= '. Did you mean one of these?';

with(new Error($output))->render($message);
with(new BulletList($output))->render($alternatives);

$output->writeln('');
} else {
with(new Error($output))->render($message);
}

return;
}

(new ConsoleApplication)->renderThrowable($e, $output);
}






public function dontReportDuplicates()
{
$this->withoutDuplicates = true;

return $this;
}







protected function isHttpException(Throwable $e)
{
return $e instanceof HttpExceptionInterface;
}







protected function mapLogLevel(Throwable $e)
{
return Arr::first(
$this->levels, fn ($level, $type) => $e instanceof $type, LogLevel::ERROR
);
}






protected function newLogger()
{
return $this->container->make(LoggerInterface::class);
}
}
