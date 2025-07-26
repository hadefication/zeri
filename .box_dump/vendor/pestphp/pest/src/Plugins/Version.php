<?php

declare(strict_types=1);

namespace Pest\Plugins;

use Pest\Contracts\Plugins\HandlesArguments;
use Pest\Support\View;

use function Pest\version;




final class Version implements HandlesArguments
{
use Concerns\HandleArguments;




public function handleArguments(array $arguments): array
{
if ($this->hasArgument('--version', $arguments)) {
View::render('version', [
'version' => version(),
]);

exit(0);
}

return $arguments;
}
}
