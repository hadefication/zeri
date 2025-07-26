<?php

declare(strict_types=1);

namespace Pest\Plugin;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Pest\Plugin\Commands\DumpCommand;




final class PestCommandProvider implements CommandProviderCapability
{



public function getCommands(): array
{
return [
new DumpCommand,
];
}
}
