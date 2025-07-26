<?php

namespace Illuminate\Foundation\Exceptions\Renderer;

use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Exceptions\Renderer\Mappers\BladeMapper;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;
use Throwable;

class Renderer
{





protected const DIST = __DIR__.'/../../resources/exceptions/renderer/dist/';






protected $viewFactory;






protected $listener;






protected $htmlErrorRenderer;






protected $bladeMapper;






protected $basePath;










public function __construct(
Factory $viewFactory,
Listener $listener,
HtmlErrorRenderer $htmlErrorRenderer,
BladeMapper $bladeMapper,
string $basePath,
) {
$this->viewFactory = $viewFactory;
$this->listener = $listener;
$this->htmlErrorRenderer = $htmlErrorRenderer;
$this->bladeMapper = $bladeMapper;
$this->basePath = $basePath;
}








public function render(Request $request, Throwable $throwable)
{
$flattenException = $this->bladeMapper->map(
$this->htmlErrorRenderer->render($throwable),
);

return $this->viewFactory->make('laravel-exceptions-renderer::show', [
'exception' => new Exception($flattenException, $request, $this->listener, $this->basePath),
])->render();
}






public static function css()
{
return (new Collection([
['styles.css', []],
['light-mode.css', ['data-theme' => 'light']],
['dark-mode.css', ['data-theme' => 'dark']],
]))->map(function ($fileAndAttributes) {
[$filename, $attributes] = $fileAndAttributes;

return '<style '.(new Collection($attributes))->map(function ($value, $attribute) {
return $attribute.'="'.$value.'"';
})->implode(' ').'>'
.file_get_contents(static::DIST.$filename)
.'</style>';
})->implode('');
}






public static function js()
{
$viteJsAutoRefresh = '';

$vite = app(\Illuminate\Foundation\Vite::class);

if (is_file($vite->hotFile())) {
$viteJsAutoRefresh = $vite->__invoke([]);
}

return '<script>'
.file_get_contents(static::DIST.'scripts.js')
.'</script>'.$viteJsAutoRefresh;
}
}
