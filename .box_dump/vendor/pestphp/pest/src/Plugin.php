<?php

declare(strict_types=1);

namespace Pest;

final class Plugin
{







public static array $callables = [];






public static function uses(string ...$traits): void
{
self::$callables[] = function () use ($traits): void {
uses(...$traits)->in(TestSuite::getInstance()->rootPath);
};
}
}
