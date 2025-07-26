<?php











declare(strict_types=1);

namespace Ramsey\Uuid;

use DateTimeInterface;
use Ramsey\Uuid\Builder\UuidBuilderInterface;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Generator\DceSecurityGeneratorInterface;
use Ramsey\Uuid\Generator\DefaultTimeGenerator;
use Ramsey\Uuid\Generator\NameGeneratorInterface;
use Ramsey\Uuid\Generator\RandomGeneratorInterface;
use Ramsey\Uuid\Generator\TimeGeneratorInterface;
use Ramsey\Uuid\Generator\UnixTimeGenerator;
use Ramsey\Uuid\Lazy\LazyUuidFromString;
use Ramsey\Uuid\Provider\NodeProviderInterface;
use Ramsey\Uuid\Provider\Time\FixedTimeProvider;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\Type\Time;
use Ramsey\Uuid\Validator\ValidatorInterface;

use function bin2hex;
use function hex2bin;
use function pack;
use function str_pad;
use function strtolower;
use function substr;
use function substr_replace;
use function unpack;

use const STR_PAD_LEFT;

class UuidFactory implements UuidFactoryInterface
{
private CodecInterface $codec;
private DceSecurityGeneratorInterface $dceSecurityGenerator;
private NameGeneratorInterface $nameGenerator;
private NodeProviderInterface $nodeProvider;
private NumberConverterInterface $numberConverter;
private RandomGeneratorInterface $randomGenerator;
private TimeConverterInterface $timeConverter;
private TimeGeneratorInterface $timeGenerator;
private TimeGeneratorInterface $unixTimeGenerator;
private UuidBuilderInterface $uuidBuilder;
private ValidatorInterface $validator;




private bool $isDefaultFeatureSet;




public function __construct(?FeatureSet $features = null)
{
$this->isDefaultFeatureSet = $features === null;

$features = $features ?: new FeatureSet();

$this->codec = $features->getCodec();
$this->dceSecurityGenerator = $features->getDceSecurityGenerator();
$this->nameGenerator = $features->getNameGenerator();
$this->nodeProvider = $features->getNodeProvider();
$this->numberConverter = $features->getNumberConverter();
$this->randomGenerator = $features->getRandomGenerator();
$this->timeConverter = $features->getTimeConverter();
$this->timeGenerator = $features->getTimeGenerator();
$this->uuidBuilder = $features->getBuilder();
$this->validator = $features->getValidator();
$this->unixTimeGenerator = $features->getUnixTimeGenerator();
}




public function getCodec(): CodecInterface
{
return $this->codec;
}






public function setCodec(CodecInterface $codec): void
{
$this->isDefaultFeatureSet = false;

$this->codec = $codec;
}




public function getNameGenerator(): NameGeneratorInterface
{
return $this->nameGenerator;
}






public function setNameGenerator(NameGeneratorInterface $nameGenerator): void
{
$this->isDefaultFeatureSet = false;

$this->nameGenerator = $nameGenerator;
}




public function getNodeProvider(): NodeProviderInterface
{
return $this->nodeProvider;
}




public function getRandomGenerator(): RandomGeneratorInterface
{
return $this->randomGenerator;
}




public function getTimeGenerator(): TimeGeneratorInterface
{
return $this->timeGenerator;
}






public function setTimeGenerator(TimeGeneratorInterface $generator): void
{
$this->isDefaultFeatureSet = false;

$this->timeGenerator = $generator;
}




public function getDceSecurityGenerator(): DceSecurityGeneratorInterface
{
return $this->dceSecurityGenerator;
}







public function setDceSecurityGenerator(DceSecurityGeneratorInterface $generator): void
{
$this->isDefaultFeatureSet = false;

$this->dceSecurityGenerator = $generator;
}




public function getNumberConverter(): NumberConverterInterface
{
return $this->numberConverter;
}






public function setRandomGenerator(RandomGeneratorInterface $generator): void
{
$this->isDefaultFeatureSet = false;

$this->randomGenerator = $generator;
}







public function setNumberConverter(NumberConverterInterface $converter): void
{
$this->isDefaultFeatureSet = false;

$this->numberConverter = $converter;
}




public function getUuidBuilder(): UuidBuilderInterface
{
return $this->uuidBuilder;
}






public function setUuidBuilder(UuidBuilderInterface $builder): void
{
$this->isDefaultFeatureSet = false;

$this->uuidBuilder = $builder;
}

public function getValidator(): ValidatorInterface
{
return $this->validator;
}






public function setValidator(ValidatorInterface $validator): void
{
$this->isDefaultFeatureSet = false;

$this->validator = $validator;
}

/**
@pure
*/
public function fromBytes(string $bytes): UuidInterface
{
return $this->codec->decodeBytes($bytes);
}

/**
@pure
*/
public function fromString(string $uuid): UuidInterface
{
$uuid = strtolower($uuid);

return $this->codec->decode($uuid);
}

/**
@pure
*/
public function fromInteger(string $integer): UuidInterface
{
$hex = $this->numberConverter->toHex($integer);
$hex = str_pad($hex, 32, '0', STR_PAD_LEFT);

return $this->fromString($hex);
}

public function fromDateTime(
DateTimeInterface $dateTime,
?Hexadecimal $node = null,
?int $clockSeq = null,
): UuidInterface {
$timeProvider = new FixedTimeProvider(new Time($dateTime->format('U'), $dateTime->format('u')));
$timeGenerator = new DefaultTimeGenerator($this->nodeProvider, $this->timeConverter, $timeProvider);
$bytes = $timeGenerator->generate($node?->toString(), $clockSeq);

return $this->uuidFromBytesAndVersion($bytes, Uuid::UUID_TYPE_TIME);
}

/**
@pure
*/
public function fromHexadecimal(Hexadecimal $hex): UuidInterface
{
return $this->codec->decode($hex->__toString());
}




public function uuid1($node = null, ?int $clockSeq = null): UuidInterface
{
$bytes = $this->timeGenerator->generate($node, $clockSeq);

return $this->uuidFromBytesAndVersion($bytes, Uuid::UUID_TYPE_TIME);
}

public function uuid2(
int $localDomain,
?IntegerObject $localIdentifier = null,
?Hexadecimal $node = null,
?int $clockSeq = null,
): UuidInterface {
$bytes = $this->dceSecurityGenerator->generate($localDomain, $localIdentifier, $node, $clockSeq);

return $this->uuidFromBytesAndVersion($bytes, Uuid::UUID_TYPE_DCE_SECURITY);
}

/**
@pure

*/
public function uuid3($ns, string $name): UuidInterface
{
return $this->uuidFromNsAndName($ns, $name, Uuid::UUID_TYPE_HASH_MD5, 'md5');
}

public function uuid4(): UuidInterface
{
$bytes = $this->randomGenerator->generate(16);

return $this->uuidFromBytesAndVersion($bytes, Uuid::UUID_TYPE_RANDOM);
}

/**
@pure

*/
public function uuid5($ns, string $name): UuidInterface
{
return $this->uuidFromNsAndName($ns, $name, Uuid::UUID_TYPE_HASH_SHA1, 'sha1');
}

public function uuid6(?Hexadecimal $node = null, ?int $clockSeq = null): UuidInterface
{
$bytes = $this->timeGenerator->generate($node?->toString(), $clockSeq);


$v6 = $bytes[6] . $bytes[7] . $bytes[4] . $bytes[5]
. $bytes[0] . $bytes[1] . $bytes[2] . $bytes[3];
$v6 = bin2hex($v6);



$v6Bytes = hex2bin(substr($v6, 1, 12) . '0' . substr($v6, -3));
$v6Bytes .= substr($bytes, 8);

return $this->uuidFromBytesAndVersion($v6Bytes, Uuid::UUID_TYPE_REORDERED_TIME);
}









public function uuid7(?DateTimeInterface $dateTime = null): UuidInterface
{
assert($this->unixTimeGenerator instanceof UnixTimeGenerator);
$bytes = $this->unixTimeGenerator->generate(null, null, $dateTime);

return $this->uuidFromBytesAndVersion($bytes, Uuid::UUID_TYPE_UNIX_TIME);
}

/**
@pure











*/
public function uuid8(string $bytes): UuidInterface
{
/**
@phpstan-ignore */
return $this->uuidFromBytesAndVersion($bytes, Uuid::UUID_TYPE_CUSTOM);
}

/**
@pure








*/
public function uuid(string $bytes): UuidInterface
{
return $this->uuidBuilder->build($this->codec, $bytes);
}

/**
@pure









*/
private function uuidFromNsAndName(
UuidInterface | string $ns,
string $name,
int $version,
string $hashAlgorithm,
): UuidInterface {
if (!($ns instanceof UuidInterface)) {
$ns = $this->fromString($ns);
}

$bytes = $this->nameGenerator->generate($ns, $name, $hashAlgorithm);

/**
@phpstan-ignore */
return $this->uuidFromBytesAndVersion(substr($bytes, 0, 16), $version);
}









private function uuidFromBytesAndVersion(string $bytes, int $version): UuidInterface
{

$unpackedTime = unpack('n*', substr($bytes, 6, 2));
$timeHi = $unpackedTime[1];
$timeHiAndVersion = pack('n*', BinaryUtils::applyVersion($timeHi, $version));


$unpackedClockSeq = unpack('n*', substr($bytes, 8, 2));
$clockSeqHi = $unpackedClockSeq[1];
$clockSeqHiAndReserved = pack('n*', BinaryUtils::applyVariant($clockSeqHi));

$bytes = substr_replace($bytes, $timeHiAndVersion, 6, 2);
$bytes = substr_replace($bytes, $clockSeqHiAndReserved, 8, 2);

if ($this->isDefaultFeatureSet) {
return LazyUuidFromString::fromBytes($bytes);
}

return $this->uuid($bytes);
}
}
