<?php

declare(strict_types=1);










namespace NunoMaduro\Collision\Adapters\Phpunit\Subscribers;

use NunoMaduro\Collision\Adapters\Phpunit\Printers\ReportablePrinter;




abstract class Subscriber
{



private ReportablePrinter $printer;




public function __construct(ReportablePrinter $printer)
{
$this->printer = $printer;
}




protected function printer(): ReportablePrinter
{
return $this->printer;
}
}
