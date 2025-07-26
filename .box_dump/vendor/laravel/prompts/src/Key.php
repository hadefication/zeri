<?php

namespace Laravel\Prompts;

class Key
{
const UP = "\e[A";

const SHIFT_UP = "\e[1;2A";

const PAGE_UP = "\e[5~";

const DOWN = "\e[B";

const SHIFT_DOWN = "\e[1;2B";

const PAGE_DOWN = "\e[6~";

const RIGHT = "\e[C";

const LEFT = "\e[D";

const UP_ARROW = "\eOA";

const DOWN_ARROW = "\eOB";

const RIGHT_ARROW = "\eOC";

const LEFT_ARROW = "\eOD";

const ESCAPE = "\e";

const DELETE = "\e[3~";

const BACKSPACE = "\177";

const ENTER = "\n";

const SPACE = ' ';

const TAB = "\t";

const SHIFT_TAB = "\e[Z";

const HOME = ["\e[1~", "\eOH", "\e[H", "\e[7~"];

const END = ["\e[4~", "\eOF", "\e[F", "\e[8~"];




const CTRL_C = "\x03";




const CTRL_P = "\x10";




const CTRL_N = "\x0E";




const CTRL_F = "\x06";




const CTRL_B = "\x02";




const CTRL_H = "\x08";




const CTRL_A = "\x01";




const CTRL_D = "\x04";




const CTRL_E = "\x05";




const CTRL_U = "\x15";






public static function oneOf(array $keys, string $match): ?string
{
foreach ($keys as $key) {
if (is_array($key) && static::oneOf($key, $match) !== null) {
return $match;
} elseif ($key === $match) {
return $match;
}
}

return null;
}
}
