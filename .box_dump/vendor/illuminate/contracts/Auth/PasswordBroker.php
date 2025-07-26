<?php

namespace Illuminate\Contracts\Auth;

use Closure;

interface PasswordBroker
{





const RESET_LINK_SENT = 'passwords.sent';






const PASSWORD_RESET = 'passwords.reset';






const INVALID_USER = 'passwords.user';






const INVALID_TOKEN = 'passwords.token';






const RESET_THROTTLED = 'passwords.throttled';








public function sendResetLink(array $credentials, ?Closure $callback = null);








public function reset(array $credentials, Closure $callback);
}
