<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Provider\Node;

use Ramsey\Uuid\Exception\NodeException;
use Ramsey\Uuid\Provider\NodeProviderInterface;
use Ramsey\Uuid\Type\Hexadecimal;




class FallbackNodeProvider implements NodeProviderInterface
{



public function __construct(private iterable $providers)
{
}

public function getNode(): Hexadecimal
{
$lastProviderException = null;

foreach ($this->providers as $provider) {
try {
return $provider->getNode();
} catch (NodeException $exception) {
$lastProviderException = $exception;

continue;
}
}

throw new NodeException(message: 'Unable to find a suitable node provider', previous: $lastProviderException);
}
}
