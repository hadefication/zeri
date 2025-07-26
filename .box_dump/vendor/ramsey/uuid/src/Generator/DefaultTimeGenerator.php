<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Generator;

use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Exception\RandomSourceException;
use Ramsey\Uuid\Exception\TimeSourceException;
use Ramsey\Uuid\Provider\NodeProviderInterface;
use Ramsey\Uuid\Provider\TimeProviderInterface;
use Ramsey\Uuid\Type\Hexadecimal;
use Throwable;

use function dechex;
use function hex2bin;
use function is_int;
use function pack;
use function preg_match;
use function sprintf;
use function str_pad;
use function strlen;

use const STR_PAD_LEFT;




class DefaultTimeGenerator implements TimeGeneratorInterface
{
public function __construct(
private NodeProviderInterface $nodeProvider,
private TimeConverterInterface $timeConverter,
private TimeProviderInterface $timeProvider,
) {
}







public function generate($node = null, ?int $clockSeq = null): string
{
if ($node instanceof Hexadecimal) {
$node = $node->toString();
}

$node = $this->getValidNode($node);

if ($clockSeq === null) {
try {

$clockSeq = random_int(0, 0x3fff);
} catch (Throwable $exception) {
throw new RandomSourceException($exception->getMessage(), (int) $exception->getCode(), $exception);
}
}

$time = $this->timeProvider->getTime();

$uuidTime = $this->timeConverter->calculateTime(
$time->getSeconds()->toString(),
$time->getMicroseconds()->toString()
);

$timeHex = str_pad($uuidTime->toString(), 16, '0', STR_PAD_LEFT);

if (strlen($timeHex) !== 16) {
throw new TimeSourceException(sprintf('The generated time of \'%s\' is larger than expected', $timeHex));
}

$timeBytes = (string) hex2bin($timeHex);

return $timeBytes[4] . $timeBytes[5] . $timeBytes[6] . $timeBytes[7]
. $timeBytes[2] . $timeBytes[3] . $timeBytes[0] . $timeBytes[1]
. pack('n*', $clockSeq) . $node;
}










private function getValidNode(int | string | null $node): string
{
if ($node === null) {
$node = $this->nodeProvider->getNode();
}


if (is_int($node)) {
$node = dechex($node);
}

if (!preg_match('/^[A-Fa-f0-9]+$/', (string) $node) || strlen((string) $node) > 12) {
throw new InvalidArgumentException('Invalid node value');
}

return (string) hex2bin(str_pad((string) $node, 12, '0', STR_PAD_LEFT));
}
}
