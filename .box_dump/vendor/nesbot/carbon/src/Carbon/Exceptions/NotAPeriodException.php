<?php

declare(strict_types=1);










namespace Carbon\Exceptions;

use InvalidArgumentException as BaseInvalidArgumentException;

class NotAPeriodException extends BaseInvalidArgumentException implements InvalidArgumentException
{

}
