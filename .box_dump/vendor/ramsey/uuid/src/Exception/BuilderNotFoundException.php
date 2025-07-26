<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Exception;

use RuntimeException as PhpRuntimeException;




class BuilderNotFoundException extends PhpRuntimeException implements UuidExceptionInterface
{
}
