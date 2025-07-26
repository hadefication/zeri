<?php










namespace Symfony\Contracts\Service;

use Psr\Container\ContainerInterface;

/**
@template-covariant





*/
interface ServiceProviderInterface extends ContainerInterface
{



public function get(string $id): mixed;

public function has(string $id): bool;












public function getProvidedServices(): array;
}
