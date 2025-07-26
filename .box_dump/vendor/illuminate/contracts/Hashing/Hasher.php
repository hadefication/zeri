<?php

namespace Illuminate\Contracts\Hashing;

interface Hasher
{






public function info($hashedValue);








public function make(#[\SensitiveParameter] $value, array $options = []);









public function check(#[\SensitiveParameter] $value, $hashedValue, array $options = []);








public function needsRehash($hashedValue, array $options = []);
}
