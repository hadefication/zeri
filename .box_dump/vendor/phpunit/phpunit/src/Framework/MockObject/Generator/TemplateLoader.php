<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Generator;

use SebastianBergmann\Template\Template;

/**
@no-named-arguments


*/
trait TemplateLoader
{



private static array $templates = [];

private function loadTemplate(string $template): Template
{
$filename = __DIR__ . '/templates/' . $template;

if (!isset(self::$templates[$filename])) {
self::$templates[$filename] = new Template($filename);
}

return self::$templates[$filename];
}
}
