<?php










namespace Symfony\Component\VarDumper\Caster;

use Symfony\Component\VarDumper\Cloner\Stub;






class ConstStub extends Stub
{
public function __construct(string $name, string|int|float|null $value = null)
{
$this->class = $name;
$this->value = 1 < \func_num_args() ? $value : $name;
}

public function __toString(): string
{
return (string) $this->value;
}




public static function fromBitfield(int $value, array $values): self
{
$names = [];
foreach ($values as $v => $name) {
if ($value & $v) {
$names[] = $name;
}
}

if (!$names) {
$names[] = $values[0] ?? 0;
}

return new self(implode(' | ', $names), $value);
}
}
