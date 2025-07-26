<?php

declare(strict_types=1);










namespace NunoMaduro\Collision\Adapters\Laravel;

use Whoops\Exception\Inspector as BaseInspector;




final class Inspector extends BaseInspector
{



protected function getTrace($e)
{
return $e->getTrace();
}
}
