<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Exceptions;

use RuntimeException;




final class ShouldNotHappen extends RuntimeException
{



private const MESSAGE = 'This should not happen, please open an issue on collision repository: %s';




public function __construct()
{
parent::__construct(sprintf(self::MESSAGE, 'https://github.com/nunomaduro/collision/issues/new'));
}
}
