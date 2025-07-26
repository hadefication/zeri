<?php

namespace Laravel\Prompts\Concerns;

trait Colors
{



public function reset(string $text): string
{
return "\e[0m{$text}\e[0m";
}




public function bold(string $text): string
{
return "\e[1m{$text}\e[22m";
}




public function dim(string $text): string
{
return "\e[2m{$text}\e[22m";
}




public function italic(string $text): string
{
return "\e[3m{$text}\e[23m";
}




public function underline(string $text): string
{
return "\e[4m{$text}\e[24m";
}




public function inverse(string $text): string
{
return "\e[7m{$text}\e[27m";
}




public function hidden(string $text): string
{
return "\e[8m{$text}\e[28m";
}




public function strikethrough(string $text): string
{
return "\e[9m{$text}\e[29m";
}




public function black(string $text): string
{
return "\e[30m{$text}\e[39m";
}




public function red(string $text): string
{
return "\e[31m{$text}\e[39m";
}




public function green(string $text): string
{
return "\e[32m{$text}\e[39m";
}




public function yellow(string $text): string
{
return "\e[33m{$text}\e[39m";
}




public function blue(string $text): string
{
return "\e[34m{$text}\e[39m";
}




public function magenta(string $text): string
{
return "\e[35m{$text}\e[39m";
}




public function cyan(string $text): string
{
return "\e[36m{$text}\e[39m";
}




public function white(string $text): string
{
return "\e[37m{$text}\e[39m";
}




public function bgBlack(string $text): string
{
return "\e[40m{$text}\e[49m";
}




public function bgRed(string $text): string
{
return "\e[41m{$text}\e[49m";
}




public function bgGreen(string $text): string
{
return "\e[42m{$text}\e[49m";
}




public function bgYellow(string $text): string
{
return "\e[43m{$text}\e[49m";
}




public function bgBlue(string $text): string
{
return "\e[44m{$text}\e[49m";
}




public function bgMagenta(string $text): string
{
return "\e[45m{$text}\e[49m";
}




public function bgCyan(string $text): string
{
return "\e[46m{$text}\e[49m";
}




public function bgWhite(string $text): string
{
return "\e[47m{$text}\e[49m";
}




public function gray(string $text): string
{
return "\e[90m{$text}\e[39m";
}
}
