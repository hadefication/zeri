<?php

declare(strict_types=1);

namespace Pest\Arch\Support;

use PHPUnit\Framework\Assert;
use ReflectionClass;
use ReflectionProperty;




final class AssertLocker
{



private static int $count = 0;




public static function incrementAndLock(): void
{

Assert::assertTrue(true);

self::$count = Assert::getCount();
}




public static function unlock(): void
{
$reflection = self::reflection();

$reflection->setValue(null, self::$count);
}




private static function reflection(): ReflectionProperty
{
$reflectionClass = new ReflectionClass(Assert::class);

$property = $reflectionClass->getProperty('count');
$property->setAccessible(true);

return $property;
}
}
