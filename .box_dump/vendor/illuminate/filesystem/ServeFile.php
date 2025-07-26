<?php

namespace Illuminate\Filesystem;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\PathTraversalDetected;

class ServeFile
{



public function __construct(
protected string $disk,
protected array $config,
protected bool $isProduction,
) {

}




public function __invoke(Request $request, string $path)
{
abort_unless(
$this->hasValidSignature($request),
$this->isProduction ? 404 : 403
);
try {
abort_unless(Storage::disk($this->disk)->exists($path), 404);

$headers = [
'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
'Content-Security-Policy' => "default-src 'none'; style-src 'unsafe-inline'; sandbox",
];

return tap(
Storage::disk($this->disk)->serve($request, $path, headers: $headers),
function ($response) use ($headers) {
if (! $response->headers->has('Content-Security-Policy')) {
$response->headers->replace($headers);
}
}
);
} catch (PathTraversalDetected $e) {
abort(404);
}
}




protected function hasValidSignature(Request $request): bool
{
return ($this->config['visibility'] ?? 'private') === 'public' ||
$request->hasValidRelativeSignature();
}
}
