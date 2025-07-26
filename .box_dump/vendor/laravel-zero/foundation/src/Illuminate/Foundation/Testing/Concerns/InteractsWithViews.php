<?php

namespace Illuminate\Foundation\Testing\Concerns;

use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Testing\TestComponent;
use Illuminate\Testing\TestView;
use Illuminate\View\View;

trait InteractsWithViews
{







protected function view(string $view, $data = [])
{
return new TestView(view($view, $data));
}








protected function blade(string $template, $data = [])
{
$tempDirectory = sys_get_temp_dir();

if (! in_array($tempDirectory, ViewFacade::getFinder()->getPaths())) {
ViewFacade::addLocation(sys_get_temp_dir());
}

$tempFileInfo = pathinfo(tempnam($tempDirectory, 'laravel-blade'));

$tempFile = $tempFileInfo['dirname'].'/'.$tempFileInfo['filename'].'.blade.php';

file_put_contents($tempFile, $template);

return new TestView(view($tempFileInfo['filename'], $data));
}








protected function component(string $componentClass, $data = [])
{
$component = $this->app->make($componentClass, $data);

$view = value($component->resolveView(), $data);

$view = $view instanceof View
? $view->with($component->data())
: view($view, $component->data());

return new TestComponent($component, $view);
}








protected function withViewErrors(array $errors, $key = 'default')
{
ViewFacade::share('errors', (new ViewErrorBag)->put($key, new MessageBag($errors)));

return $this;
}
}
