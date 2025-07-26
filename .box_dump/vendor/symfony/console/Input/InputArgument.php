<?php










namespace Symfony\Component\Console\Input;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Completion\Suggestion;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;






class InputArgument
{



public const REQUIRED = 1;




public const OPTIONAL = 2;




public const IS_ARRAY = 4;

private int $mode;
private string|int|bool|array|float|null $default;










public function __construct(
private string $name,
?int $mode = null,
private string $description = '',
string|bool|int|float|array|null $default = null,
private \Closure|array $suggestedValues = [],
) {
if (null === $mode) {
$mode = self::OPTIONAL;
} elseif ($mode >= (self::IS_ARRAY << 1) || $mode < 1) {
throw new InvalidArgumentException(\sprintf('Argument mode "%s" is not valid.', $mode));
}

$this->mode = $mode;

$this->setDefault($default);
}




public function getName(): string
{
return $this->name;
}






public function isRequired(): bool
{
return self::REQUIRED === (self::REQUIRED & $this->mode);
}






public function isArray(): bool
{
return self::IS_ARRAY === (self::IS_ARRAY & $this->mode);
}




public function setDefault(string|bool|int|float|array|null $default): void
{
if ($this->isRequired() && null !== $default) {
throw new LogicException('Cannot set a default value except for InputArgument::OPTIONAL mode.');
}

if ($this->isArray()) {
if (null === $default) {
$default = [];
} elseif (!\is_array($default)) {
throw new LogicException('A default value for an array argument must be an array.');
}
}

$this->default = $default;
}




public function getDefault(): string|bool|int|float|array|null
{
return $this->default;
}




public function hasCompletion(): bool
{
return [] !== $this->suggestedValues;
}






public function complete(CompletionInput $input, CompletionSuggestions $suggestions): void
{
$values = $this->suggestedValues;
if ($values instanceof \Closure && !\is_array($values = $values($input))) {
throw new LogicException(\sprintf('Closure for argument "%s" must return an array. Got "%s".', $this->name, get_debug_type($values)));
}
if ($values) {
$suggestions->suggestValues($values);
}
}




public function getDescription(): string
{
return $this->description;
}
}
