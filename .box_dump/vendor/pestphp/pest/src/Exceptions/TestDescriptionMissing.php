<?php

declare(strict_types=1);

namespace Pest\Exceptions;

use InvalidArgumentException;
use NunoMaduro\Collision\Contracts\RenderlessEditor;
use NunoMaduro\Collision\Contracts\RenderlessTrace;
use Symfony\Component\Console\Exception\ExceptionInterface;




final class TestDescriptionMissing extends InvalidArgumentException implements ExceptionInterface, RenderlessEditor, RenderlessTrace
{



public function __construct(string $fileName)
{
parent::__construct(sprintf('Test description is missing in the filename `%s`.', $fileName));
}
}
