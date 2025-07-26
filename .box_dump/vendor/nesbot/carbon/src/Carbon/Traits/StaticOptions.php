<?php

declare(strict_types=1);










namespace Carbon\Traits;

use Carbon\FactoryImmutable;




trait StaticOptions
{









protected static $formatFunction;






protected static $createFromFormatFunction;






protected static $parseFunction;














public static function useStrictMode(bool $strictModeEnabled = true): void
{
FactoryImmutable::getDefaultInstance()->useStrictMode($strictModeEnabled);
}







public static function isStrictModeEnabled(): bool
{
return FactoryImmutable::getInstance()->isStrictModeEnabled();
}














public static function useMonthsOverflow(bool $monthsOverflow = true): void
{
FactoryImmutable::getDefaultInstance()->useMonthsOverflow($monthsOverflow);
}












public static function resetMonthsOverflow(): void
{
FactoryImmutable::getDefaultInstance()->resetMonthsOverflow();
}






public static function shouldOverflowMonths(): bool
{
return FactoryImmutable::getInstance()->shouldOverflowMonths();
}














public static function useYearsOverflow(bool $yearsOverflow = true): void
{
FactoryImmutable::getDefaultInstance()->useYearsOverflow($yearsOverflow);
}












public static function resetYearsOverflow(): void
{
FactoryImmutable::getDefaultInstance()->resetYearsOverflow();
}






public static function shouldOverflowYears(): bool
{
return FactoryImmutable::getInstance()->shouldOverflowYears();
}
}
