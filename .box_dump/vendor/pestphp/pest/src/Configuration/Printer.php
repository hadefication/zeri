<?php

declare(strict_types=1);

namespace Pest\Configuration;

use NunoMaduro\Collision\Adapters\Phpunit\Printers\DefaultPrinter;




final readonly class Printer
{



public function compact(): self
{
DefaultPrinter::compact(true);

return $this;
}
}
