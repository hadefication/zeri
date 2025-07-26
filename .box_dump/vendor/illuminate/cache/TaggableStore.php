<?php

namespace Illuminate\Cache;

use Illuminate\Contracts\Cache\Store;

abstract class TaggableStore implements Store
{






public function tags($names)
{
return new TaggedCache($this, new TagSet($this, is_array($names) ? $names : func_get_args()));
}
}
