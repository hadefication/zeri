<?php










namespace Symfony\Component\Translation\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Translation\TranslatorBagInterface;




class DataCollectorTranslatorPass implements CompilerPassInterface
{
public function process(ContainerBuilder $container): void
{
if (!$container->has('translator')) {
return;
}

$translatorClass = $container->getParameterBag()->resolveValue($container->findDefinition('translator')->getClass());

if (!is_subclass_of($translatorClass, TranslatorBagInterface::class)) {
$container->removeDefinition('translator.data_collector');
$container->removeDefinition('data_collector.translation');
}
}
}
