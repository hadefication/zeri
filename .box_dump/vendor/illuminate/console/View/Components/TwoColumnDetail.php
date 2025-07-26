<?php

namespace Illuminate\Console\View\Components;

use Symfony\Component\Console\Output\OutputInterface;

class TwoColumnDetail extends Component
{








public function render($first, $second = null, $verbosity = OutputInterface::VERBOSITY_NORMAL)
{
$first = $this->mutate($first, [
Mutators\EnsureDynamicContentIsHighlighted::class,
Mutators\EnsureNoPunctuation::class,
Mutators\EnsureRelativePaths::class,
]);

$second = $this->mutate($second, [
Mutators\EnsureDynamicContentIsHighlighted::class,
Mutators\EnsureNoPunctuation::class,
Mutators\EnsureRelativePaths::class,
]);

$this->renderView('two-column-detail', [
'first' => $first,
'second' => $second,
], $verbosity);
}
}
