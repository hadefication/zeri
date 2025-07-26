<?php









use Mockery\LegacyMockInterface;
use Mockery\Matcher\AndAnyOtherArgs;
use Mockery\Matcher\AnyArgs;
use Mockery\MockInterface;

if (! \function_exists('mock')) {
/**
@template




*/
function mock(...$args)
{
return Mockery::mock(...$args);
}
}

if (! \function_exists('spy')) {
/**
@template




*/
function spy(...$args)
{
return Mockery::spy(...$args);
}
}

if (! \function_exists('namedMock')) {
/**
@template




*/
function namedMock(...$args)
{
return Mockery::namedMock(...$args);
}
}

if (! \function_exists('anyArgs')) {
function anyArgs(): AnyArgs
{
return new AnyArgs();
}
}

if (! \function_exists('andAnyOtherArgs')) {
function andAnyOtherArgs(): AndAnyOtherArgs
{
return new AndAnyOtherArgs();
}
}

if (! \function_exists('andAnyOthers')) {
function andAnyOthers(): AndAnyOtherArgs
{
return new AndAnyOtherArgs();
}
}
