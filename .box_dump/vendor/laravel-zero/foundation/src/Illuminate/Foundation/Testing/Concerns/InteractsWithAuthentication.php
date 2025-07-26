<?php

namespace Illuminate\Foundation\Testing\Concerns;

use Illuminate\Contracts\Auth\Authenticatable as UserContract;

trait InteractsWithAuthentication
{







public function actingAs(UserContract $user, $guard = null)
{
return $this->be($user, $guard);
}








public function be(UserContract $user, $guard = null)
{
if (isset($user->wasRecentlyCreated) && $user->wasRecentlyCreated) {
$user->wasRecentlyCreated = false;
}

$this->app['auth']->guard($guard)->setUser($user);

$this->app['auth']->shouldUse($guard);

return $this;
}







public function assertAuthenticated($guard = null)
{
$this->assertTrue($this->isAuthenticated($guard), 'The user is not authenticated');

return $this;
}







public function assertGuest($guard = null)
{
$this->assertFalse($this->isAuthenticated($guard), 'The user is authenticated');

return $this;
}







protected function isAuthenticated($guard = null)
{
return $this->app->make('auth')->guard($guard)->check();
}








public function assertAuthenticatedAs($user, $guard = null)
{
$expected = $this->app->make('auth')->guard($guard)->user();

$this->assertNotNull($expected, 'The current user is not authenticated.');

$this->assertInstanceOf(
get_class($expected), $user,
'The currently authenticated user is not who was expected'
);

$this->assertSame(
$expected->getAuthIdentifier(), $user->getAuthIdentifier(),
'The currently authenticated user is not who was expected'
);

return $this;
}








public function assertCredentials(array $credentials, $guard = null)
{
$this->assertTrue(
$this->hasCredentials($credentials, $guard), 'The given credentials are invalid.'
);

return $this;
}








public function assertInvalidCredentials(array $credentials, $guard = null)
{
$this->assertFalse(
$this->hasCredentials($credentials, $guard), 'The given credentials are valid.'
);

return $this;
}








protected function hasCredentials(array $credentials, $guard = null)
{
$provider = $this->app->make('auth')->guard($guard)->getProvider();

$user = $provider->retrieveByCredentials($credentials);

return $user && $provider->validateCredentials($user, $credentials);
}
}
