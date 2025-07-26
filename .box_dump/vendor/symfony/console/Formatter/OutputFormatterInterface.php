<?php










namespace Symfony\Component\Console\Formatter;






interface OutputFormatterInterface
{



public function setDecorated(bool $decorated): void;




public function isDecorated(): bool;




public function setStyle(string $name, OutputFormatterStyleInterface $style): void;




public function hasStyle(string $name): bool;






public function getStyle(string $name): OutputFormatterStyleInterface;




public function format(?string $message): ?string;
}
