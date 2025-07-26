<?php declare(strict_types=1);

namespace PhpParser;




class Token extends Internal\TokenPolyfill {

public function getEndPos(): int {
return $this->pos + \strlen($this->text);
}


public function getEndLine(): int {
return $this->line + \substr_count($this->text, "\n");
}
}
