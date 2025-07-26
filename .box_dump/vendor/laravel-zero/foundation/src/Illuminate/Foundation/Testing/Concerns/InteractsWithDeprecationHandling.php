<?php

namespace Illuminate\Foundation\Testing\Concerns;

use ErrorException;

trait InteractsWithDeprecationHandling
{





protected $originalDeprecationHandler;






protected function withDeprecationHandling()
{
if ($this->originalDeprecationHandler) {
set_error_handler(tap($this->originalDeprecationHandler, fn () => $this->originalDeprecationHandler = null));
}

return $this;
}






protected function withoutDeprecationHandling()
{
if ($this->originalDeprecationHandler == null) {
$this->originalDeprecationHandler = set_error_handler(function ($level, $message, $file = '', $line = 0) {
if (in_array($level, [E_DEPRECATED, E_USER_DEPRECATED]) || (error_reporting() & $level)) {
throw new ErrorException($message, 0, $level, $file, $line);
}
});
}

return $this;
}
}
