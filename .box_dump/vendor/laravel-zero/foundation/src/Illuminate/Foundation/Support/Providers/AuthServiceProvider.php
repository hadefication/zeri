<?php

namespace Illuminate\Foundation\Support\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{





protected $policies = [];






public function register()
{
$this->booting(function () {
$this->registerPolicies();
});
}






public function registerPolicies()
{
foreach ($this->policies() as $model => $policy) {
Gate::policy($model, $policy);
}
}






public function policies()
{
return $this->policies;
}
}
