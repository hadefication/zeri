<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Codec;

use Ramsey\Uuid\UuidInterface;

/**
@immutable


*/
interface CodecInterface
{
/**
@pure






*/
public function encode(UuidInterface $uuid): string;

/**
@pure






*/
public function encodeBinary(UuidInterface $uuid): string;

/**
@pure






*/
public function decode(string $encodedUuid): UuidInterface;

/**
@pure






*/
public function decodeBytes(string $bytes): UuidInterface;
}
