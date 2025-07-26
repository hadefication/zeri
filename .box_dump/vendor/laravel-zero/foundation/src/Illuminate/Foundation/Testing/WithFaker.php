<?php

namespace Illuminate\Foundation\Testing;

use Faker\Factory;
use Faker\Generator;

trait WithFaker
{





protected $faker;






protected function setUpFaker()
{
$this->faker = $this->makeFaker();
}







protected function faker($locale = null)
{
return is_null($locale) ? $this->faker : $this->makeFaker($locale);
}







protected function makeFaker($locale = null)
{
if (isset($this->app)) {
$locale ??= $this->app->make('config')->get('app.faker_locale', Factory::DEFAULT_LOCALE);

if ($this->app->bound(Generator::class)) {
return $this->app->make(Generator::class, ['locale' => $locale]);
}
}

return Factory::create($locale ?? Factory::DEFAULT_LOCALE);
}
}
