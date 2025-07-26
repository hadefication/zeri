<?php










namespace Symfony\Component\Console\Helper;






final class TreeStyle
{
public function __construct(
private readonly string $prefixEndHasNext,
private readonly string $prefixEndLast,
private readonly string $prefixLeft,
private readonly string $prefixMidHasNext,
private readonly string $prefixMidLast,
private readonly string $prefixRight,
) {
}

public static function box(): self
{
return new self('┃╸ ', '┗╸ ', '', '┃  ', '   ', '');
}

public static function boxDouble(): self
{
return new self('╠═ ', '╚═ ', '', '║  ', '  ', '');
}

public static function compact(): self
{
return new self('├ ', '└ ', '', '│ ', '  ', '');
}

public static function default(): self
{
return new self('├── ', '└── ', '', '│   ', '   ', '');
}

public static function light(): self
{
return new self('|-- ', '`-- ', '', '|   ', '    ', '');
}

public static function minimal(): self
{
return new self('. ', '. ', '', '. ', '  ', '');
}

public static function rounded(): self
{
return new self('├─ ', '╰─ ', '', '│  ', '   ', '');
}




public function applyPrefixes(\RecursiveTreeIterator $iterator): void
{
$iterator->setPrefixPart(\RecursiveTreeIterator::PREFIX_LEFT, $this->prefixLeft);
$iterator->setPrefixPart(\RecursiveTreeIterator::PREFIX_MID_HAS_NEXT, $this->prefixMidHasNext);
$iterator->setPrefixPart(\RecursiveTreeIterator::PREFIX_MID_LAST, $this->prefixMidLast);
$iterator->setPrefixPart(\RecursiveTreeIterator::PREFIX_END_HAS_NEXT, $this->prefixEndHasNext);
$iterator->setPrefixPart(\RecursiveTreeIterator::PREFIX_END_LAST, $this->prefixEndLast);
$iterator->setPrefixPart(\RecursiveTreeIterator::PREFIX_RIGHT, $this->prefixRight);
}
}
