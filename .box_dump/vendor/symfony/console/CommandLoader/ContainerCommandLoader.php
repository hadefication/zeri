<?php










namespace Symfony\Component\Console\CommandLoader;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\CommandNotFoundException;






class ContainerCommandLoader implements CommandLoaderInterface
{



public function __construct(
private ContainerInterface $container,
private array $commandMap,
) {
}

public function get(string $name): Command
{
if (!$this->has($name)) {
throw new CommandNotFoundException(\sprintf('Command "%s" does not exist.', $name));
}

return $this->container->get($this->commandMap[$name]);
}

public function has(string $name): bool
{
return isset($this->commandMap[$name]) && $this->container->has($this->commandMap[$name]);
}

public function getNames(): array
{
return array_keys($this->commandMap);
}
}
