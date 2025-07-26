<?php

namespace Illuminate\Foundation\Testing\Traits;

trait CanConfigureMigrationCommands
{





protected function migrateFreshUsing()
{
$seeder = $this->seeder();

return array_merge(
[
'--drop-views' => $this->shouldDropViews(),
'--drop-types' => $this->shouldDropTypes(),
],
$seeder ? ['--seeder' => $seeder] : ['--seed' => $this->shouldSeed()]
);
}






protected function shouldDropViews()
{
return property_exists($this, 'dropViews') ? $this->dropViews : false;
}






protected function shouldDropTypes()
{
return property_exists($this, 'dropTypes') ? $this->dropTypes : false;
}






protected function shouldSeed()
{
return property_exists($this, 'seed') ? $this->seed : false;
}






protected function seeder()
{
return property_exists($this, 'seeder') ? $this->seeder : false;
}
}
