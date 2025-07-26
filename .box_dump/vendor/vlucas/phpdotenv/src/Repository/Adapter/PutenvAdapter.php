<?php

declare(strict_types=1);

namespace Dotenv\Repository\Adapter;

use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;

final class PutenvAdapter implements AdapterInterface
{





private function __construct()
{

}






public static function create()
{
if (self::isSupported()) {

return Some::create(new self());
}

return None::create();
}






private static function isSupported()
{
return \function_exists('getenv') && \function_exists('putenv');
}








public function read(string $name)
{

return Option::fromValue(\getenv($name), false)->filter(static function ($value) {
return \is_string($value);
});
}









public function write(string $name, string $value)
{
\putenv("$name=$value");

return true;
}








public function delete(string $name)
{
\putenv($name);

return true;
}
}
