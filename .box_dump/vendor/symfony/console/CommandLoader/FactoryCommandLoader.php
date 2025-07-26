<?php










namespace Symfony\Component\Console\CommandLoader;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\CommandNotFoundException;






class FactoryCommandLoader implements CommandLoaderInterface
{



public function __construct(
private array $factories,
) {
}

public function has(string $name): bool
{
return isset($this->factories[$name]);
}

public function get(string $name): Command
{
if (!isset($this->factories[$name])) {
throw new CommandNotFoundException(\sprintf('Command "%s" does not exist.', $name));
}

$factory = $this->factories[$name];

return $factory();
}

public function getNames(): array
{
return array_keys($this->factories);
}
}
