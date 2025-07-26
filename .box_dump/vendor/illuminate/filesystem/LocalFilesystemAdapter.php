<?php

namespace Illuminate\Filesystem;

use Closure;
use Illuminate\Support\Traits\Conditionable;
use RuntimeException;

class LocalFilesystemAdapter extends FilesystemAdapter
{
use Conditionable;






protected $disk;






protected $shouldServeSignedUrls = false;






protected $urlGeneratorResolver;






public function providesTemporaryUrls()
{
return $this->temporaryUrlCallback || (
$this->shouldServeSignedUrls && $this->urlGeneratorResolver instanceof Closure
);
}









public function temporaryUrl($path, $expiration, array $options = [])
{
if ($this->temporaryUrlCallback) {
return $this->temporaryUrlCallback->bindTo($this, static::class)(
$path, $expiration, $options
);
}

if (! $this->providesTemporaryUrls()) {
throw new RuntimeException('This driver does not support creating temporary URLs.');
}

$url = call_user_func($this->urlGeneratorResolver);

return $url->to($url->temporarySignedRoute(
'storage.'.$this->disk,
$expiration,
['path' => $path],
absolute: false
));
}







public function diskName(string $disk)
{
$this->disk = $disk;

return $this;
}








public function shouldServeSignedUrls(bool $serve = true, ?Closure $urlGeneratorResolver = null)
{
$this->shouldServeSignedUrls = $serve;
$this->urlGeneratorResolver = $urlGeneratorResolver;

return $this;
}
}
