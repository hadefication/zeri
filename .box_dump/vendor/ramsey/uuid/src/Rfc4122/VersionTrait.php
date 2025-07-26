<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Rfc4122;

use Ramsey\Uuid\Uuid;

/**
@immutable


*/
trait VersionTrait
{
/**
@pure


















*/
abstract public function getVersion(): ?int;




abstract public function isMax(): bool;




abstract public function isNil(): bool;






private function isCorrectVersion(): bool
{
if ($this->isNil() || $this->isMax()) {
return true;
}

return match ($this->getVersion()) {
Uuid::UUID_TYPE_TIME, Uuid::UUID_TYPE_DCE_SECURITY,
Uuid::UUID_TYPE_HASH_MD5, Uuid::UUID_TYPE_RANDOM,
Uuid::UUID_TYPE_HASH_SHA1, Uuid::UUID_TYPE_REORDERED_TIME,
Uuid::UUID_TYPE_UNIX_TIME, Uuid::UUID_TYPE_CUSTOM => true,
default => false,
};
}
}
