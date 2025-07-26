<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Type;

/**
@immutable


*/
interface NumberInterface extends TypeInterface
{



public function isNegative(): bool;
}
