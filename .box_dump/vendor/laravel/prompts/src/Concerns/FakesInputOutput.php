<?php

namespace Laravel\Prompts\Concerns;

use Laravel\Prompts\Output\BufferedConsoleOutput;
use Laravel\Prompts\Terminal;
use PHPUnit\Framework\Assert;
use RuntimeException;

trait FakesInputOutput
{





public static function fake(array $keys = []): void
{

static::interactive();

$mock = \Mockery::mock(Terminal::class);

$mock->shouldReceive('write')->byDefault();
$mock->shouldReceive('exit')->byDefault();
$mock->shouldReceive('setTty')->byDefault();
$mock->shouldReceive('restoreTty')->byDefault();
$mock->shouldReceive('cols')->byDefault()->andReturn(80);
$mock->shouldReceive('lines')->byDefault()->andReturn(24);
$mock->shouldReceive('initDimensions')->byDefault();

static::fakeKeyPresses($keys, function (string $key) use ($mock): void {
$mock->shouldReceive('read')->once()->andReturn($key);
});

static::$terminal = $mock;

self::setOutput(new BufferedConsoleOutput);
}











public static function fakeKeyPresses(array $keys, callable $callable): void
{
foreach ($keys as $key) {
$callable($key);
}
}




public static function assertOutputContains(string $string): void
{
Assert::assertStringContainsString($string, static::content());
}




public static function assertOutputDoesntContain(string $string): void
{
Assert::assertStringNotContainsString($string, static::content());
}




public static function assertStrippedOutputContains(string $string): void
{
Assert::assertStringContainsString($string, static::strippedContent());
}




public static function assertStrippedOutputDoesntContain(string $string): void
{
Assert::assertStringNotContainsString($string, static::strippedContent());
}




public static function content(): string
{
if (! static::output() instanceof BufferedConsoleOutput) {
throw new RuntimeException('Prompt must be faked before accessing content.');
}

return static::output()->content();
}




public static function strippedContent(): string
{
return preg_replace("/\e\[[0-9;?]*[A-Za-z]/", '', static::content());
}
}
