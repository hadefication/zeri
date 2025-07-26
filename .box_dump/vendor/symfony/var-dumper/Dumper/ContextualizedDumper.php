<?php










namespace Symfony\Component\VarDumper\Dumper;

use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\Dumper\ContextProvider\ContextProviderInterface;




class ContextualizedDumper implements DataDumperInterface
{



public function __construct(
private DataDumperInterface $wrappedDumper,
private array $contextProviders,
) {
}

public function dump(Data $data): ?string
{
$context = $data->getContext();
foreach ($this->contextProviders as $contextProvider) {
$context[$contextProvider::class] = $contextProvider->getContext();
}

return $this->wrappedDumper->dump($data->withContext($context));
}
}
