<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Provider\Node;

use Ramsey\Uuid\Exception\RandomSourceException;
use Ramsey\Uuid\Provider\NodeProviderInterface;
use Ramsey\Uuid\Type\Hexadecimal;
use Throwable;

use function bin2hex;
use function dechex;
use function hex2bin;
use function hexdec;
use function str_pad;
use function substr;

use const STR_PAD_LEFT;






class RandomNodeProvider implements NodeProviderInterface
{
public function getNode(): Hexadecimal
{
try {
$nodeBytes = random_bytes(6);
} catch (Throwable $exception) {
throw new RandomSourceException($exception->getMessage(), (int) $exception->getCode(), $exception);
}


$nodeMsb = substr($nodeBytes, 0, 3);
$nodeLsb = substr($nodeBytes, 3);


$nodeMsb = hex2bin(str_pad(dechex(hexdec(bin2hex($nodeMsb)) | 0x010000), 6, '0', STR_PAD_LEFT));

return new Hexadecimal(str_pad(bin2hex($nodeMsb . $nodeLsb), 12, '0', STR_PAD_LEFT));
}
}
