<?php

declare(strict_types=1);










namespace Carbon\Traits;

use Carbon\FactoryImmutable;
use Closure;






trait ToStringFormat
{





public static function resetToStringFormat(): void
{
FactoryImmutable::getDefaultInstance()->resetToStringFormat();
}













public static function setToStringFormat(string|Closure|null $format): void
{
FactoryImmutable::getDefaultInstance()->setToStringFormat($format);
}
}
