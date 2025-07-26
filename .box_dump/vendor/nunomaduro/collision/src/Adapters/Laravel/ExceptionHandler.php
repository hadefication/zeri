<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Laravel;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use NunoMaduro\Collision\Provider;
use Symfony\Component\Console\Exception\ExceptionInterface as SymfonyConsoleExceptionInterface;
use Throwable;




final class ExceptionHandler implements ExceptionHandlerContract
{





protected $appExceptionHandler;






protected $container;




public function __construct(Container $container, ExceptionHandlerContract $appExceptionHandler)
{
$this->container = $container;
$this->appExceptionHandler = $appExceptionHandler;
}




public function report(Throwable $e)
{
$this->appExceptionHandler->report($e);
}




public function render($request, Throwable $e)
{
return $this->appExceptionHandler->render($request, $e);
}




public function renderForConsole($output, Throwable $e)
{
if ($e instanceof SymfonyConsoleExceptionInterface) {
$this->appExceptionHandler->renderForConsole($output, $e);
} else {

$provider = $this->container->make(Provider::class);

$handler = $provider->register()
->getHandler()
->setOutput($output);

$handler->setInspector((new Inspector($e)));

$handler->handle();
}
}






public function shouldReport(Throwable $e)
{
return $this->appExceptionHandler->shouldReport($e);
}






public function reportable(callable $reportUsing)
{
return $this->appExceptionHandler->reportable($reportUsing);
}






public function renderable(callable $renderUsing)
{
$this->appExceptionHandler->renderable($renderUsing);

return $this;
}






public function dontReportDuplicates()
{
$this->appExceptionHandler->dontReportDuplicates();

return $this;
}
}
