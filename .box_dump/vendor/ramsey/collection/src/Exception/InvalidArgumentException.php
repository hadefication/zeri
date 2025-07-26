<?php











declare(strict_types=1);

namespace Ramsey\Collection\Exception;

use InvalidArgumentException as PhpInvalidArgumentException;




class InvalidArgumentException extends PhpInvalidArgumentException implements CollectionException
{
}
