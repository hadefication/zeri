<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Provider;

use Ramsey\Uuid\Rfc4122\UuidV2;
use Ramsey\Uuid\Type\Integer as IntegerObject;






interface DceSecurityProviderInterface
{





public function getUid(): IntegerObject;






public function getGid(): IntegerObject;
}
