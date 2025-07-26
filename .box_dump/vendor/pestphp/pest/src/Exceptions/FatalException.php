<?php

declare(strict_types=1);

namespace Pest\Exceptions;

use NunoMaduro\Collision\Contracts\RenderlessTrace;
use RuntimeException;




final class FatalException extends RuntimeException implements RenderlessTrace
{

}
