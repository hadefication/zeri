<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Validator;

/**
@immutable


*/
interface ValidatorInterface
{





public function getPattern(): string;

/**
@pure






*/
public function validate(string $uuid): bool;
}
