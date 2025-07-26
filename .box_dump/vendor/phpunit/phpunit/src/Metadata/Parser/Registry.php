<?php declare(strict_types=1);








namespace PHPUnit\Metadata\Parser;

/**
@no-named-arguments





*/
final class Registry
{
private static ?Parser $instance = null;

public static function parser(): Parser
{
return self::$instance ?? self::$instance = self::build();
}

private static function build(): Parser
{
return new CachingParser(
new ParserChain(
new AttributeParser,
new AnnotationParser,
),
);
}
}
