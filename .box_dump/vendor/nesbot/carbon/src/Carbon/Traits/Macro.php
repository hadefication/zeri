<?php

declare(strict_types=1);










namespace Carbon\Traits;

use Carbon\FactoryImmutable;






trait Macro
{
use Mixin;

/**
@param-closure-this
















*/
public static function macro(string $name, ?callable $macro): void
{
FactoryImmutable::getDefaultInstance()->macro($name, $macro);
}




public static function resetMacros(): void
{
FactoryImmutable::getDefaultInstance()->resetMacros();
}









public static function genericMacro(callable $macro, int $priority = 0): void
{
FactoryImmutable::getDefaultInstance()->genericMacro($macro, $priority);
}








public static function hasMacro(string $name): bool
{
return FactoryImmutable::getInstance()->hasMacro($name);
}




public static function getMacro(string $name): ?callable
{
return FactoryImmutable::getInstance()->getMacro($name);
}




public function hasLocalMacro(string $name): bool
{
return ($this->localMacros && isset($this->localMacros[$name])) || $this->transmitFactory(
static fn () => static::hasMacro($name),
);
}




public function getLocalMacro(string $name): ?callable
{
return ($this->localMacros ?? [])[$name] ?? $this->transmitFactory(
static fn () => static::getMacro($name),
);
}
}
