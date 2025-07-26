<?php

namespace Illuminate\Contracts\Database\Query;

use Illuminate\Database\Grammar;

interface Expression
{






public function getValue(Grammar $grammar);
}
