<?php

declare(strict_types=1);

namespace PHPUnit\Architecture\Asserts\Iterator;

trait IteratorAsserts
{
abstract public static function assertTrue($condition, string $message = ''): void;

/**
@template



*/
public function assertEach($list, callable $check, callable $message): void
{
foreach ($list as $key => $item) {
if (!$check($item)) {
self::assertTrue(false, $message($key, $item));
}
}

self::assertTrue(true);
}

/**
@template



*/
public function assertNotOne($list, callable $check, callable $message): void
{
foreach ($list as $key => $item) {
if ($check($item)) {
self::assertTrue(false, $message($key, $item));
}
}

self::assertTrue(true);
}

/**
@template


*/
public function assertAny($list, callable $check, string $message): void
{
$isTrue = false;
foreach ($list as $item) {
if ($check($item)) {
$isTrue = true;
}
}

self::assertTrue($isTrue, $message);
}
}
