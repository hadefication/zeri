<?php

declare(strict_types=1);

namespace Doctrine\Inflector;

class CachedWordInflector implements WordInflector
{

private $wordInflector;


private $cache = [];

public function __construct(WordInflector $wordInflector)
{
$this->wordInflector = $wordInflector;
}

public function inflect(string $word): string
{
return $this->cache[$word] ?? $this->cache[$word] = $this->wordInflector->inflect($word);
}
}
