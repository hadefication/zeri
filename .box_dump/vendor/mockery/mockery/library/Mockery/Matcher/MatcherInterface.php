<?php

declare(strict_types=1);









namespace Mockery\Matcher;

interface MatcherInterface
{





public function __toString();

/**
@template








*/
public function match(&$actual);
}
