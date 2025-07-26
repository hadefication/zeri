<?php

declare(strict_types=1);

namespace Doctrine\Inflector\Rules;

use function preg_match;

final class Pattern
{

private $pattern;


private $regex;

public function __construct(string $pattern)
{
$this->pattern = $pattern;

if (isset($this->pattern[0]) && $this->pattern[0] === '/') {
$this->regex = $this->pattern;
} else {
$this->regex = '/' . $this->pattern . '/i';
}
}

public function getPattern(): string
{
return $this->pattern;
}

public function getRegex(): string
{
return $this->regex;
}

public function matches(string $word): bool
{
return preg_match($this->getRegex(), $word) === 1;
}
}
