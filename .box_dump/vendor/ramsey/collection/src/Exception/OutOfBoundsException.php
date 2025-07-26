<?php











declare(strict_types=1);

namespace Ramsey\Collection\Exception;

use OutOfBoundsException as PhpOutOfBoundsException;




class OutOfBoundsException extends PhpOutOfBoundsException implements CollectionException
{
}
