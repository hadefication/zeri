<?php










namespace Symfony\Component\VarDumper\Dumper;

use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\Dumper\ContextProvider\ContextProviderInterface;
use Symfony\Component\VarDumper\Server\Connection;






class ServerDumper implements DataDumperInterface
{
private Connection $connection;






public function __construct(
string $host,
private ?DataDumperInterface $wrappedDumper = null,
array $contextProviders = [],
) {
$this->connection = new Connection($host, $contextProviders);
}

public function getContextProviders(): array
{
return $this->connection->getContextProviders();
}

public function dump(Data $data): ?string
{
if (!$this->connection->write($data) && $this->wrappedDumper) {
return $this->wrappedDumper->dump($data);
}

return null;
}
}
