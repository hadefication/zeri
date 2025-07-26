<?php

declare(strict_types=1);

namespace Dotenv\Repository;

interface RepositoryInterface
{







public function has(string $name);










public function get(string $name);











public function set(string $name, string $value);










public function clear(string $name);
}
