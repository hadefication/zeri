<?php

namespace Illuminate\Contracts\Cache;

interface LockProvider
{








public function lock($name, $seconds = 0, $owner = null);








public function restoreLock($name, $owner);
}
