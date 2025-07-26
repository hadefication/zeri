<?php

namespace Illuminate\Contracts\Auth;

interface UserProvider
{






public function retrieveById($identifier);








public function retrieveByToken($identifier, #[\SensitiveParameter] $token);








public function updateRememberToken(Authenticatable $user, #[\SensitiveParameter] $token);







public function retrieveByCredentials(#[\SensitiveParameter] array $credentials);








public function validateCredentials(Authenticatable $user, #[\SensitiveParameter] array $credentials);









public function rehashPasswordIfRequired(Authenticatable $user, #[\SensitiveParameter] array $credentials, bool $force = false);
}
