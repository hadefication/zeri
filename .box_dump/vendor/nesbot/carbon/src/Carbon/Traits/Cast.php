<?php

declare(strict_types=1);










namespace Carbon\Traits;

use Carbon\Exceptions\InvalidCastException;
use DateTimeInterface;






trait Cast
{
/**
@template






*/
public function cast(string $className): mixed
{
if (!method_exists($className, 'instance')) {
if (is_a($className, DateTimeInterface::class, true)) {
return $className::createFromFormat('U.u', $this->rawFormat('U.u'))
->setTimezone($this->getTimezone());
}

throw new InvalidCastException("$className has not the instance() method needed to cast the date.");
}

return $className::instance($this);
}
}
