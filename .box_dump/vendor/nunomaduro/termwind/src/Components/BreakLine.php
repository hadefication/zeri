<?php

declare(strict_types=1);

namespace Termwind\Components;

final class BreakLine extends Element
{



public function toString(): string
{
$display = $this->styles->getProperties()['styles']['display'] ?? 'inline';

if ($display === 'hidden') {
return '';
}

if ($display === 'block') {
return parent::toString();
}

return parent::toString()."\r";
}
}
