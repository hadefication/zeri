<?php

namespace LaravelZero\Framework\Providers\CommandRecorder;

use Illuminate\Support\Collection;




final class CommandRecorderRepository
{



public const MODE_SILENT = 'silent';




public const MODE_DEFAULT = 'default';






private $storage;




public function __construct(?Collection $storage = null)
{
$this->storage = $storage ?? collect();
}




public function clear(): void
{
$this->storage = collect();
}




public function create(string $command, array $arguments = [], string $mode = self::MODE_DEFAULT): void
{
$this->storage[] = [
'command' => $command,
'arguments' => $arguments,
'mode' => $mode,
];
}




public function exists(string $command, array $arguments = []): bool
{
return $this->storage->contains(function ($value) use ($command, $arguments) {
return $value['command'] === $command && $value['arguments'] === $arguments;
});
}
}
