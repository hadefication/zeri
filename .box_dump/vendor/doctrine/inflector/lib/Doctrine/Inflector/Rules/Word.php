<?php

declare(strict_types=1);

namespace Doctrine\Inflector\Rules;

class Word
{

private $word;

public function __construct(string $word)
{
$this->word = $word;
}

public function getWord(): string
{
return $this->word;
}
}
