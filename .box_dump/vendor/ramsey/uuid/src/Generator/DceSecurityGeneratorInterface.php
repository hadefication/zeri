<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Generator;

use Ramsey\Uuid\Rfc4122\UuidV2;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;







interface DceSecurityGeneratorInterface
{













public function generate(
int $localDomain,
?IntegerObject $localIdentifier = null,
?Hexadecimal $node = null,
?int $clockSeq = null,
): string;
}
