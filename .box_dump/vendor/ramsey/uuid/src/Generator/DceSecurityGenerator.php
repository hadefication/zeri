<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Generator;

use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Exception\DceSecurityException;
use Ramsey\Uuid\Provider\DceSecurityProviderInterface;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\Uuid;

use function hex2bin;
use function in_array;
use function pack;
use function str_pad;
use function strlen;
use function substr_replace;

use const STR_PAD_LEFT;





class DceSecurityGenerator implements DceSecurityGeneratorInterface
{
private const DOMAINS = [
Uuid::DCE_DOMAIN_PERSON,
Uuid::DCE_DOMAIN_GROUP,
Uuid::DCE_DOMAIN_ORG,
];




private const CLOCK_SEQ_HIGH = 63;




private const CLOCK_SEQ_LOW = 0;

public function __construct(
private NumberConverterInterface $numberConverter,
private TimeGeneratorInterface $timeGenerator,
private DceSecurityProviderInterface $dceSecurityProvider,
) {
}

public function generate(
int $localDomain,
?IntegerObject $localIdentifier = null,
?Hexadecimal $node = null,
?int $clockSeq = null,
): string {
if (!in_array($localDomain, self::DOMAINS)) {
throw new DceSecurityException('Local domain must be a valid DCE Security domain');
}

if ($localIdentifier && $localIdentifier->isNegative()) {
throw new DceSecurityException(
'Local identifier out of bounds; it must be a value between 0 and 4294967295',
);
}

if ($clockSeq > self::CLOCK_SEQ_HIGH || $clockSeq < self::CLOCK_SEQ_LOW) {
throw new DceSecurityException('Clock sequence out of bounds; it must be a value between 0 and 63');
}

switch ($localDomain) {
case Uuid::DCE_DOMAIN_ORG:
if ($localIdentifier === null) {
throw new DceSecurityException('A local identifier must be provided for the org domain');
}

break;
case Uuid::DCE_DOMAIN_PERSON:
if ($localIdentifier === null) {
$localIdentifier = $this->dceSecurityProvider->getUid();
}

break;
case Uuid::DCE_DOMAIN_GROUP:
default:
if ($localIdentifier === null) {
$localIdentifier = $this->dceSecurityProvider->getGid();
}

break;
}

$identifierHex = $this->numberConverter->toHex($localIdentifier->toString());



if (strlen($identifierHex) > 8) {
throw new DceSecurityException(
'Local identifier out of bounds; it must be a value between 0 and 4294967295',
);
}

$domainByte = pack('n', $localDomain)[1];
$identifierBytes = (string) hex2bin(str_pad($identifierHex, 8, '0', STR_PAD_LEFT));

if ($node instanceof Hexadecimal) {
$node = $node->toString();
}


if ($clockSeq !== null) {
$clockSeq = $clockSeq << 8;
}

$bytes = $this->timeGenerator->generate($node, $clockSeq);


$bytes = substr_replace($bytes, $identifierBytes, 0, 4);

return substr_replace($bytes, $domainByte, 9, 1);
}
}
