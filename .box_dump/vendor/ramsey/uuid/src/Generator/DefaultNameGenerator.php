<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Generator;

use Ramsey\Uuid\Exception\NameException;
use Ramsey\Uuid\UuidInterface;
use ValueError;

use function hash;




class DefaultNameGenerator implements NameGeneratorInterface
{
/**
@pure
*/
public function generate(UuidInterface $ns, string $name, string $hashAlgorithm): string
{
try {
return hash($hashAlgorithm, $ns->getBytes() . $name, true);
} catch (ValueError $e) {
throw new NameException(
message: sprintf('Unable to hash namespace and name with algorithm \'%s\'', $hashAlgorithm),
previous: $e,
);
}
}
}
