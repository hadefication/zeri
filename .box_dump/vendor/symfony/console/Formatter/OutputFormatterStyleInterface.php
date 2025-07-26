<?php










namespace Symfony\Component\Console\Formatter;






interface OutputFormatterStyleInterface
{



public function setForeground(?string $color): void;




public function setBackground(?string $color): void;




public function setOption(string $option): void;




public function unsetOption(string $option): void;




public function setOptions(array $options): void;




public function apply(string $text): string;
}
