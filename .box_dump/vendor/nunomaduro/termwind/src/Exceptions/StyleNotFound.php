<?php

declare(strict_types=1);

namespace Termwind\Exceptions;

use InvalidArgumentException;




final class StyleNotFound extends InvalidArgumentException
{



private function __construct(string $message)
{
parent::__construct($message, 0, $this->getPrevious());
}




public static function fromStyle(string $style): self
{
return new self(sprintf('Style [%s] not found.', $style));
}
}
