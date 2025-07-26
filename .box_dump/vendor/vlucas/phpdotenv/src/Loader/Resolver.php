<?php

declare(strict_types=1);

namespace Dotenv\Loader;

use Dotenv\Parser\Value;
use Dotenv\Repository\RepositoryInterface;
use Dotenv\Util\Regex;
use Dotenv\Util\Str;
use PhpOption\Option;

final class Resolver
{







private function __construct()
{

}












public static function resolve(RepositoryInterface $repository, Value $value)
{
return \array_reduce($value->getVars(), static function (string $s, int $i) use ($repository) {
return Str::substr($s, 0, $i).self::resolveVariable($repository, Str::substr($s, $i));
}, $value->getChars());
}









private static function resolveVariable(RepositoryInterface $repository, string $str)
{
return Regex::replaceCallback(
'/\A\${([a-zA-Z0-9_.]+)}/',
static function (array $matches) use ($repository) {

return Option::fromValue($repository->get($matches[1]))->getOrElse($matches[0]);
},
$str,
1
)->success()->getOrElse($str);
}
}
