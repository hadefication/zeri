<?php










namespace Symfony\Component\Translation\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Reference;




class TranslationExtractorPass implements CompilerPassInterface
{
public function process(ContainerBuilder $container): void
{
if (!$container->hasDefinition('translation.extractor')) {
return;
}

$definition = $container->getDefinition('translation.extractor');

foreach ($container->findTaggedServiceIds('translation.extractor', true) as $id => $attributes) {
if (!isset($attributes[0]['alias'])) {
throw new RuntimeException(\sprintf('The alias for the tag "translation.extractor" of service "%s" must be set.', $id));
}

$definition->addMethodCall('addExtractor', [$attributes[0]['alias'], new Reference($id)]);
}
}
}
