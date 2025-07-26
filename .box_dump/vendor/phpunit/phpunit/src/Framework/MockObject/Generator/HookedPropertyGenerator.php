<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Generator;

use function sprintf;

/**
@no-named-arguments


*/
final class HookedPropertyGenerator
{






public function generate(string $className, array $properties): string
{
$code = '';

foreach ($properties as $property) {
$code .= sprintf(
<<<'EOT'

    public %s $%s {
EOT,
$property->type()->asString(),
$property->name(),
);

if ($property->hasGetHook()) {
$code .= sprintf(
<<<'EOT'

        get {
            return $this->__phpunit_getInvocationHandler()->invoke(
                new \PHPUnit\Framework\MockObject\Invocation(
                    '%s', '$%s::get', [], '%s', $this, false
                )
            );
        }

EOT,
$className,
$property->name(),
$property->type()->asString(),
);
}

if ($property->hasSetHook()) {
$code .= sprintf(
<<<'EOT'

        set (%s $value) {
            $this->__phpunit_getInvocationHandler()->invoke(
                new \PHPUnit\Framework\MockObject\Invocation(
                    '%s', '$%s::set', [$value], 'void', $this, false
                )
            );
        }

EOT,
$property->type()->asString(),
$className,
$property->name(),
);
}

$code .= <<<'EOT'
    }

EOT;
}

return $code;
}
}
