<?php

namespace Illuminate\Console;

interface CommandMutex
{






public function create($command);







public function exists($command);







public function forget($command);
}
