<?php










namespace Symfony\Component\Console\Formatter;




final class NullOutputFormatterStyle implements OutputFormatterStyleInterface
{
public function apply(string $text): string
{
return $text;
}

public function setBackground(?string $color): void
{

}

public function setForeground(?string $color): void
{

}

public function setOption(string $option): void
{

}

public function setOptions(array $options): void
{

}

public function unsetOption(string $option): void
{

}
}
