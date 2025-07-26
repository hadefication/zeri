<?php

declare(strict_types=1);

namespace Dotenv\Repository\Adapter;

use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;

final class ApacheAdapter implements AdapterInterface
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
return \function_exists('apache_getenv') && \function_exists('apache_setenv');
}








public function read(string $name)
{

return Option::fromValue(apache_getenv($name))->filter(static function ($value) {
return \is_string($value) && $value !== '';
});
}









public function write(string $name, string $value)
{
return apache_setenv($name, $value);
}








public function delete(string $name)
{
return apache_setenv($name, '');
}
}
