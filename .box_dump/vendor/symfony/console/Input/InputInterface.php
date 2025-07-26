<?php










namespace Symfony\Component\Console\Input;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\RuntimeException;






interface InputInterface
{



public function getFirstArgument(): ?string;












public function hasParameterOption(string|array $values, bool $onlyParams = false): bool;













public function getParameterOption(string|array $values, string|bool|int|float|array|null $default = false, bool $onlyParams = false): mixed;






public function bind(InputDefinition $definition): void;






public function validate(): void;






public function getArguments(): array;






public function getArgument(string $name): mixed;






public function setArgument(string $name, mixed $value): void;




public function hasArgument(string $name): bool;






public function getOptions(): array;






public function getOption(string $name): mixed;






public function setOption(string $name, mixed $value): void;




public function hasOption(string $name): bool;




public function isInteractive(): bool;




public function setInteractive(bool $interactive): void;






public function __toString(): string;
}
