<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Generator;

use function uuid_create;
use function uuid_parse;

use const UUID_TYPE_TIME;






class PeclUuidTimeGenerator implements TimeGeneratorInterface
{



public function generate($node = null, ?int $clockSeq = null): string
{
$uuid = uuid_create(UUID_TYPE_TIME);

return (string) uuid_parse($uuid);
}
}
