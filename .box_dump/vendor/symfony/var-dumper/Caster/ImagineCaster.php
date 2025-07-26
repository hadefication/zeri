<?php










namespace Symfony\Component\VarDumper\Caster;

use Imagine\Image\ImageInterface;
use Symfony\Component\VarDumper\Cloner\Stub;






final class ImagineCaster
{
public static function castImage(ImageInterface $c, array $a, Stub $stub, bool $isNested): array
{
$imgData = $c->get('png');
if (\strlen($imgData) > 1 * 1000 * 1000) {
$a += [
Caster::PREFIX_VIRTUAL.'image' => new ConstStub($c->getSize()),
];
} else {
$a += [
Caster::PREFIX_VIRTUAL.'image' => new ImgStub($imgData, 'image/png', $c->getSize()),
];
}

return $a;
}
}
