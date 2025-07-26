<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Contracts\Adapters\Phpunit;




interface HasPrintableTestCaseName
{



public static function getPrintableTestCaseName(): string;




public function getPrintableTestCaseMethodName(): string;




public static function getLatestPrintableTestCaseMethodName(): string;
}
