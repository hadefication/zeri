<?php










namespace Symfony\Component\EventDispatcher\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;






class AddEventAliasesPass implements CompilerPassInterface
{
public function __construct(
private array $eventAliases,
) {
}

public function process(ContainerBuilder $container): void
{
$eventAliases = $container->hasParameter('event_dispatcher.event_aliases') ? $container->getParameter('event_dispatcher.event_aliases') : [];

$container->setParameter(
'event_dispatcher.event_aliases',
array_merge($eventAliases, $this->eventAliases)
);
}
}
