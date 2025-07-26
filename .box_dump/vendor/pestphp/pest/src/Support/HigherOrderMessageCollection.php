<?php

declare(strict_types=1);

namespace Pest\Support;




final class HigherOrderMessageCollection
{



private array $messages = [];






public function add(string $filename, int $line, string $name, ?array $arguments): void
{
$this->messages[] = new HigherOrderMessage($filename, $line, $name, $arguments);
}






public function addWhen(callable $condition, string $filename, int $line, string $name, ?array $arguments): void
{
$this->messages[] = (new HigherOrderMessage($filename, $line, $name, $arguments))->when($condition);
}




public function chain(object $target): void
{
foreach ($this->messages as $message) {
$target = $message->call($target) ?? $target;
}
}




public function proxy(object $target): void
{
foreach ($this->messages as $message) {
$message->call($target);
}
}






public function count(string $name): int
{
return array_reduce(
$this->messages,
static fn (int $total, HigherOrderMessage $message): int => $total + (int) ($name === $message->name),
0,
);
}
}
