<?php

namespace Illuminate\Console\View\Components;

use Symfony\Component\Console\Output\OutputInterface;

class Alert extends Component
{







public function render($string, $verbosity = OutputInterface::VERBOSITY_NORMAL)
{
$string = $this->mutate($string, [
Mutators\EnsureDynamicContentIsHighlighted::class,
Mutators\EnsurePunctuation::class,
Mutators\EnsureRelativePaths::class,
]);

$this->renderView('alert', [
'content' => $string,
], $verbosity);
}
}
