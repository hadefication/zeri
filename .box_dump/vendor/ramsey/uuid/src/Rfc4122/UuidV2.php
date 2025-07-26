<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Rfc4122;

use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Rfc4122\FieldsInterface as Rfc4122FieldsInterface;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\Uuid;

use function hexdec;

/**
@immutable




















*/
final class UuidV2 extends Uuid implements UuidInterface
{
use TimeTrait;










public function __construct(
Rfc4122FieldsInterface $fields,
NumberConverterInterface $numberConverter,
CodecInterface $codec,
TimeConverterInterface $timeConverter,
) {
if ($fields->getVersion() !== Uuid::UUID_TYPE_DCE_SECURITY) {
throw new InvalidArgumentException(
'Fields used to create a UuidV2 must represent a version 2 (DCE Security) UUID'
);
}

parent::__construct($fields, $numberConverter, $codec, $timeConverter);
}




public function getLocalDomain(): int
{

$fields = $this->getFields();

return (int) hexdec($fields->getClockSeqLow()->toString());
}




public function getLocalDomainName(): string
{
return Uuid::DCE_DOMAIN_NAMES[$this->getLocalDomain()];
}




public function getLocalIdentifier(): IntegerObject
{

$fields = $this->getFields();

return new IntegerObject($this->numberConverter->fromHex($fields->getTimeLow()->toString()));
}
}
