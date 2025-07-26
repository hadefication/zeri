<?php










namespace Symfony\Component\Translation\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;




class TranslationDumperPass implements CompilerPassInterface
{
public function process(ContainerBuilder $container): void
{
if (!$container->hasDefinition('translation.writer')) {
return;
}

$definition = $container->getDefinition('translation.writer');

foreach ($container->findTaggedServiceIds('translation.dumper', true) as $id => $attributes) {
$definition->addMethodCall('addDumper', [$attributes[0]['alias'], new Reference($id)]);
}
}
}
