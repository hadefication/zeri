<?php

declare(strict_types=1);

namespace Termwind\Components;




final class Raw extends Element
{



public function toString(): string
{
return is_array($this->content) ? implode('', $this->content) : $this->content;
}
}
