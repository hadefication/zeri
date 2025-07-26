<?php

namespace Illuminate\Contracts\Encryption;

interface StringEncrypter
{








public function encryptString(#[\SensitiveParameter] $value);









public function decryptString($payload);
}
