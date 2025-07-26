<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Provider\Node;

use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Provider\NodeProviderInterface;
use Ramsey\Uuid\Type\Hexadecimal;

use function dechex;
use function hexdec;
use function str_pad;
use function substr;

use const STR_PAD_LEFT;






class StaticNodeProvider implements NodeProviderInterface
{
private Hexadecimal $node;




public function __construct(Hexadecimal $node)
{
if (strlen($node->toString()) > 12) {
throw new InvalidArgumentException('Static node value cannot be greater than 12 hexadecimal characters');
}

$this->node = $this->setMulticastBit($node);
}

public function getNode(): Hexadecimal
{
return $this->node;
}




private function setMulticastBit(Hexadecimal $node): Hexadecimal
{
$nodeHex = str_pad($node->toString(), 12, '0', STR_PAD_LEFT);
$firstOctet = substr($nodeHex, 0, 2);
$firstOctet = str_pad(dechex(hexdec($firstOctet) | 0x01), 2, '0', STR_PAD_LEFT);

return new Hexadecimal($firstOctet . substr($nodeHex, 2));
}
}
