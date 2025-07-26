<?php

declare(strict_types=1);

namespace Dotenv\Repository\Adapter;

use PhpOption\Option;
use PhpOption\Some;

final class EnvConstAdapter implements AdapterInterface
{





private function __construct()
{

}






public static function create()
{

return Some::create(new self());
}








public function read(string $name)
{

return Option::fromArraysValue($_ENV, $name)
->filter(static function ($value) {
return \is_scalar($value);
})
->map(static function ($value) {
if ($value === false) {
return 'false';
}

if ($value === true) {
return 'true';
}

/**
@psalm-suppress */
return (string) $value;
});
}









public function write(string $name, string $value)
{
$_ENV[$name] = $value;

return true;
}








public function delete(string $name)
{
unset($_ENV[$name]);

return true;
}
}
