<?php

declare(strict_types=1);

namespace Dotenv\Repository\Adapter;

interface WriterInterface
{








public function write(string $name, string $value);








public function delete(string $name);
}
