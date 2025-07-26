<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Generator;

use Ramsey\Uuid\UuidInterface;





interface NameGeneratorInterface
{
/**
@pure








*/
public function generate(UuidInterface $ns, string $name, string $hashAlgorithm): string;
}
