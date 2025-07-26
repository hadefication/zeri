<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Validator;

use Ramsey\Uuid\Uuid;

use function preg_match;
use function str_replace;

/**
@immutable


*/
final class GenericValidator implements ValidatorInterface
{



private const VALID_PATTERN = '\A[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}\z';




public function getPattern(): string
{
return self::VALID_PATTERN;
}

public function validate(string $uuid): bool
{
/**
@phpstan-ignore */
$uuid = str_replace(['urn:', 'uuid:', 'URN:', 'UUID:', '{', '}'], '', $uuid);

/**
@phpstan-ignore */
return $uuid === Uuid::NIL || preg_match('/' . self::VALID_PATTERN . '/Dms', $uuid);
}
}
