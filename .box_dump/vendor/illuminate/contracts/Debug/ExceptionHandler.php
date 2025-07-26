<?php

namespace Illuminate\Contracts\Debug;

use Throwable;

interface ExceptionHandler
{








public function report(Throwable $e);







public function shouldReport(Throwable $e);










public function render($request, Throwable $e);










public function renderForConsole($output, Throwable $e);
}
