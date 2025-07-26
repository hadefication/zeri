<?php

namespace NunoMaduro\Collision\Contracts;

use Whoops\Exception\Frame;

interface RenderableOnCollisionEditor
{



public function toCollisionEditor(): Frame;
}
