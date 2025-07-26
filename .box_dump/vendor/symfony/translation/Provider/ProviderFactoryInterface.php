<?php










namespace Symfony\Component\Translation\Provider;

use Symfony\Component\Translation\Exception\IncompleteDsnException;
use Symfony\Component\Translation\Exception\UnsupportedSchemeException;

interface ProviderFactoryInterface
{




public function create(Dsn $dsn): ProviderInterface;

public function supports(Dsn $dsn): bool;
}
