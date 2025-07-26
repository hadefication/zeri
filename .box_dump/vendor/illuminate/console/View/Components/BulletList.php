<?php

namespace Illuminate\Console\View\Components;

use Symfony\Component\Console\Output\OutputInterface;

class BulletList extends Component
{







public function render($elements, $verbosity = OutputInterface::VERBOSITY_NORMAL)
{
$elements = $this->mutate($elements, [
Mutators\EnsureDynamicContentIsHighlighted::class,
Mutators\EnsureNoPunctuation::class,
Mutators\EnsureRelativePaths::class,
]);

$this->renderView('bullet-list', [
'elements' => $elements,
], $verbosity);
}
}
