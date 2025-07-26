<?php

declare(strict_types=1);

namespace Dotenv\Loader;

use Dotenv\Repository\RepositoryInterface;

interface LoaderInterface
{








public function load(RepositoryInterface $repository, array $entries);
}
