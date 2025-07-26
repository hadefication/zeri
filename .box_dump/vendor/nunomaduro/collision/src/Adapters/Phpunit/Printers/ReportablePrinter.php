<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Phpunit\Printers;

use Throwable;

/**
@mixin


*/
final class ReportablePrinter
{



public function __construct(private readonly DefaultPrinter $printer)
{

}




public function __call(string $name, array $arguments): mixed
{
try {
return $this->printer->$name(...$arguments);
} catch (Throwable $throwable) {
$this->printer->report($throwable);
}

exit(1);
}
}
