<?php

declare(strict_types=1);

namespace Dotenv\Parser;

use Dotenv\Util\Str;

final class Value
{





private $chars;






private $vars;









private function __construct(string $chars, array $vars)
{
$this->chars = $chars;
$this->vars = $vars;
}






public static function blank()
{
return new self('', []);
}









public function append(string $chars, bool $var)
{
return new self(
$this->chars.$chars,
$var ? \array_merge($this->vars, [Str::len($this->chars)]) : $this->vars
);
}






public function getChars()
{
return $this->chars;
}






public function getVars()
{
$vars = $this->vars;

\rsort($vars);

return $vars;
}
}
