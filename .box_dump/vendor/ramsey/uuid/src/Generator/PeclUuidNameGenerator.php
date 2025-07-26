<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Generator;

use Ramsey\Uuid\Exception\NameException;
use Ramsey\Uuid\UuidInterface;

use function sprintf;
use function uuid_generate_md5;
use function uuid_generate_sha1;
use function uuid_parse;






class PeclUuidNameGenerator implements NameGeneratorInterface
{
/**
@pure
*/
public function generate(UuidInterface $ns, string $name, string $hashAlgorithm): string
{
$uuid = match ($hashAlgorithm) {
'md5' => uuid_generate_md5($ns->toString(), $name), /**
@phpstan-ignore */
'sha1' => uuid_generate_sha1($ns->toString(), $name), /**
@phpstan-ignore */
default => throw new NameException(
sprintf('Unable to hash namespace and name with algorithm \'%s\'', $hashAlgorithm),
),
};

/**
@phpstan-ignore */
return (string) uuid_parse($uuid);
}
}
