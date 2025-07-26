<?php

declare(strict_types=1);

namespace Pest\Arch\ValueObjects;

final class Violation
{



public function __construct(public readonly string $path, public readonly int $start, public readonly int $end)
{

}
}
