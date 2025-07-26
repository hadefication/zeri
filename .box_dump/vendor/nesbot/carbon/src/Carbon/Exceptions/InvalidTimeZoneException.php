<?php

declare(strict_types=1);










namespace Carbon\Exceptions;

use InvalidArgumentException as BaseInvalidArgumentException;

class InvalidTimeZoneException extends BaseInvalidArgumentException implements InvalidArgumentException
{

}
