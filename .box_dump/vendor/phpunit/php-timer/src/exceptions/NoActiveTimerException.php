<?php declare(strict_types=1);








namespace SebastianBergmann\Timer;

use LogicException;

final class NoActiveTimerException extends LogicException implements Exception
{
}
