<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Fields;

use Serializable;

/**
@immutable



*/
interface FieldsInterface extends Serializable
{
/**
@pure


*/
public function getBytes(): string;
}
