<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Generator;

use function uuid_create;
use function uuid_parse;

use const UUID_TYPE_RANDOM;






class PeclUuidRandomGenerator implements RandomGeneratorInterface
{
public function generate(int $length): string
{
$uuid = uuid_create(UUID_TYPE_RANDOM);

return (string) uuid_parse($uuid);
}
}
