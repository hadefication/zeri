<?php

namespace Illuminate\View\Compilers;

use Illuminate\Container\Container;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\Support\Traits\ReflectsClosures;
use Illuminate\View\Component;
use InvalidArgumentException;

class BladeCompiler extends Compiler implements CompilerInterface
{
use Concerns\CompilesAuthorizations,
Concerns\CompilesClasses,
Concerns\CompilesComments,
Concerns\CompilesComponents,
Concerns\CompilesConditionals,
Concerns\CompilesContexts,
Concerns\CompilesEchos,
Concerns\CompilesErrors,
Concerns\CompilesFragments,
Concerns\CompilesHelpers,
Concerns\CompilesIncludes,
Concerns\CompilesInjections,
Concerns\CompilesJson,
Concerns\CompilesJs,
Concerns\CompilesLayouts,
Concerns\CompilesLoops,
Concerns\CompilesRawPhp,
Concerns\CompilesSessions,
Concerns\CompilesStacks,
Concerns\CompilesStyles,
Concerns\CompilesTranslations,
Concerns\CompilesUseStatements,
ReflectsClosures;






protected $extensions = [];






protected $customDirectives = [];






protected $conditions = [];






protected $prepareStringsForCompilationUsing = [];






protected $precompilers = [];






protected $path;






protected $compilers = [

'Extensions',
'Statements',
'Echos',
];






protected $rawTags = ['{!!', '!!}'];






protected $contentTags = ['{{', '}}'];






protected $escapedTags = ['{{{', '}}}'];






protected $echoFormat = 'e(%s)';






protected $footer = [];






protected $rawBlocks = [];






protected $anonymousComponentPaths = [];






protected $anonymousComponentNamespaces = [];






protected $classComponentAliases = [];






protected $classComponentNamespaces = [];






protected $compilesComponentTags = true;







public function compile($path = null)
{
if ($path) {
$this->setPath($path);
}

if (! is_null($this->cachePath)) {
$contents = $this->compileString($this->files->get($this->getPath()));

if (! empty($this->getPath())) {
$contents = $this->appendFilePath($contents);
}

$this->ensureCompiledDirectoryExists(
$compiledPath = $this->getCompiledPath($this->getPath())
);

if (! $this->files->exists($compiledPath)) {
$this->files->put($compiledPath, $contents);

return;
}

$compiledHash = $this->files->hash($compiledPath, 'xxh128');

if ($compiledHash !== hash('xxh128', $contents)) {
$this->files->put($compiledPath, $contents);
}
}
}







protected function appendFilePath($contents)
{
$tokens = $this->getOpenAndClosingPhpTokens($contents);

if ($tokens->isNotEmpty() && $tokens->last() !== T_CLOSE_TAG) {
$contents .= ' ?>';
}

return $contents."<?php /**PATH {$this->getPath()} ENDPATH**/ ?>";
}







protected function getOpenAndClosingPhpTokens($contents)
{
$tokens = [];

foreach (token_get_all($contents) as $token) {
if ($token[0] === T_OPEN_TAG || $token[0] === T_OPEN_TAG_WITH_ECHO || $token[0] === T_CLOSE_TAG) {
$tokens[] = $token[0];
}
}

return new Collection($tokens);
}






public function getPath()
{
return $this->path;
}







public function setPath($path)
{
$this->path = $path;
}







public function compileString($value)
{
[$this->footer, $result] = [[], ''];

foreach ($this->prepareStringsForCompilationUsing as $callback) {
$value = $callback($value);
}

$value = $this->storeUncompiledBlocks($value);




$value = $this->compileComponentTags(
$this->compileComments($value)
);

foreach ($this->precompilers as $precompiler) {
$value = $precompiler($value);
}




foreach (token_get_all($value) as $token) {
$result .= is_array($token) ? $this->parseToken($token) : $token;
}

if (! empty($this->rawBlocks)) {
$result = $this->restoreRawContent($result);
}




if (count($this->footer) > 0) {
$result = $this->addFooters($result);
}

if (! empty($this->echoHandlers)) {
$result = $this->addBladeCompilerVariable($result);
}

return str_replace(
['##BEGIN-COMPONENT-CLASS##', '##END-COMPONENT-CLASS##'],
'',
$result);
}









public static function render($string, $data = [], $deleteCachedView = false)
{
$component = new class($string) extends Component
{
protected $template;

public function __construct($template)
{
$this->template = $template;
}

public function render()
{
return $this->template;
}
};

$view = Container::getInstance()
->make(ViewFactory::class)
->make($component->resolveView(), $data);

return tap($view->render(), function () use ($view, $deleteCachedView) {
if ($deleteCachedView) {
@unlink($view->getPath());
}
});
}







public static function renderComponent(Component $component)
{
$data = $component->data();

$view = value($component->resolveView(), $data);

if ($view instanceof View) {
return $view->with($data)->render();
} elseif ($view instanceof Htmlable) {
return $view->toHtml();
} else {
return Container::getInstance()
->make(ViewFactory::class)
->make($view, $data)
->render();
}
}







protected function storeUncompiledBlocks($value)
{
if (str_contains($value, '@verbatim')) {
$value = $this->storeVerbatimBlocks($value);
}

if (str_contains($value, '@php')) {
$value = $this->storePhpBlocks($value);
}

return $value;
}







protected function storeVerbatimBlocks($value)
{
return preg_replace_callback('/(?<!@)@verbatim(\s*)(.*?)@endverbatim/s', function ($matches) {
return $matches[1].$this->storeRawBlock($matches[2]);
}, $value);
}







protected function storePhpBlocks($value)
{
return preg_replace_callback('/(?<!@)@php(.*?)@endphp/s', function ($matches) {
return $this->storeRawBlock("<?php{$matches[1]}?>");
}, $value);
}







protected function storeRawBlock($value)
{
return $this->getRawPlaceholder(
array_push($this->rawBlocks, $value) - 1
);
}







protected function compileComponentTags($value)
{
if (! $this->compilesComponentTags) {
return $value;
}

return (new ComponentTagCompiler(
$this->classComponentAliases, $this->classComponentNamespaces, $this
))->compile($value);
}







protected function restoreRawContent($result)
{
$result = preg_replace_callback('/'.$this->getRawPlaceholder('(\d+)').'/', function ($matches) {
return $this->rawBlocks[$matches[1]];
}, $result);

$this->rawBlocks = [];

return $result;
}







protected function getRawPlaceholder($replace)
{
return str_replace('#', $replace, '@__raw_block_#__@');
}







protected function addFooters($result)
{
return ltrim($result, "\n")
."\n".implode("\n", array_reverse($this->footer));
}







protected function parseToken($token)
{
[$id, $content] = $token;

if ($id == T_INLINE_HTML) {
foreach ($this->compilers as $type) {
$content = $this->{"compile{$type}"}($content);
}
}

return $content;
}







protected function compileExtensions($value)
{
foreach ($this->extensions as $compiler) {
$value = $compiler($value, $this);
}

return $value;
}







protected function compileStatements($template)
{
preg_match_all('/\B@(@?\w+(?:::\w+)?)([ \t]*)(\( ( [\S\s]*? ) \))?/x', $template, $matches);

$offset = 0;

for ($i = 0; isset($matches[0][$i]); $i++) {
$match = [
$matches[0][$i],
$matches[1][$i],
$matches[2][$i],
$matches[3][$i] ?: null,
$matches[4][$i] ?: null,
];




while (isset($match[4]) &&
Str::endsWith($match[0], ')') &&
! $this->hasEvenNumberOfParentheses($match[0])) {
if (($after = Str::after($template, $match[0])) === $template) {
break;
}

$rest = Str::before($after, ')');

if (isset($matches[0][$i + 1]) && Str::contains($rest.')', $matches[0][$i + 1])) {
unset($matches[0][$i + 1]);
$i++;
}

$match[0] = $match[0].$rest.')';
$match[3] = $match[3].$rest.')';
$match[4] = $match[4].$rest;
}

[$template, $offset] = $this->replaceFirstStatement(
$match[0],
$this->compileStatement($match),
$template,
$offset
);
}

return $template;
}










protected function replaceFirstStatement($search, $replace, $subject, $offset)
{
$search = (string) $search;

if ($search === '') {
return $subject;
}

$position = strpos($subject, $search, $offset);

if ($position !== false) {
return [
substr_replace($subject, $replace, $position, strlen($search)),
$position + strlen($replace),
];
}

return [$subject, 0];
}







protected function hasEvenNumberOfParentheses(string $expression)
{
$tokens = token_get_all('<?php '.$expression);

if (Arr::last($tokens) !== ')') {
return false;
}

$opening = 0;
$closing = 0;

foreach ($tokens as $token) {
if ($token == ')') {
$closing++;
} elseif ($token == '(') {
$opening++;
}
}

return $opening === $closing;
}







protected function compileStatement($match)
{
if (str_contains($match[1], '@')) {
$match[0] = isset($match[3]) ? $match[1].$match[3] : $match[1];
} elseif (isset($this->customDirectives[$match[1]])) {
$match[0] = $this->callCustomDirective($match[1], Arr::get($match, 3));
} elseif (method_exists($this, $method = 'compile'.ucfirst($match[1]))) {
$match[0] = $this->$method(Arr::get($match, 3));
} else {
return $match[0];
}

return isset($match[3]) ? $match[0] : $match[0].$match[2];
}








protected function callCustomDirective($name, $value)
{
$value ??= '';

if (str_starts_with($value, '(') && str_ends_with($value, ')')) {
$value = Str::substr($value, 1, -1);
}

return call_user_func($this->customDirectives[$name], trim($value));
}







public function stripParentheses($expression)
{
if (Str::startsWith($expression, '(')) {
$expression = substr($expression, 1, -1);
}

return $expression;
}







public function extend(callable $compiler)
{
$this->extensions[] = $compiler;
}






public function getExtensions()
{
return $this->extensions;
}








public function if($name, callable $callback)
{
$this->conditions[$name] = $callback;

$this->directive($name, function ($expression) use ($name) {
return $expression !== ''
? "<?php if (\Illuminate\Support\Facades\Blade::check('{$name}', {$expression})): ?>"
: "<?php if (\Illuminate\Support\Facades\Blade::check('{$name}')): ?>";
});

$this->directive('unless'.$name, function ($expression) use ($name) {
return $expression !== ''
? "<?php if (! \Illuminate\Support\Facades\Blade::check('{$name}', {$expression})): ?>"
: "<?php if (! \Illuminate\Support\Facades\Blade::check('{$name}')): ?>";
});

$this->directive('else'.$name, function ($expression) use ($name) {
return $expression !== ''
? "<?php elseif (\Illuminate\Support\Facades\Blade::check('{$name}', {$expression})): ?>"
: "<?php elseif (\Illuminate\Support\Facades\Blade::check('{$name}')): ?>";
});

$this->directive('end'.$name, function () {
return '<?php endif; ?>';
});
}








public function check($name, ...$parameters)
{
return call_user_func($this->conditions[$name], ...$parameters);
}









public function component($class, $alias = null, $prefix = '')
{
if (! is_null($alias) && str_contains($alias, '\\')) {
[$class, $alias] = [$alias, $class];
}

if (is_null($alias)) {
$alias = str_contains($class, '\\View\\Components\\')
? (new Collection(explode('\\', Str::after($class, '\\View\\Components\\'))))
->map(fn ($segment) => Str::kebab($segment))
->implode(':')
: Str::kebab(class_basename($class));
}

if (! empty($prefix)) {
$alias = $prefix.'-'.$alias;
}

$this->classComponentAliases[$alias] = $class;
}








public function components(array $components, $prefix = '')
{
foreach ($components as $key => $value) {
if (is_numeric($key)) {
$this->component($value, null, $prefix);
} else {
$this->component($key, $value, $prefix);
}
}
}






public function getClassComponentAliases()
{
return $this->classComponentAliases;
}








public function anonymousComponentPath(string $path, ?string $prefix = null)
{
$prefixHash = hash('xxh128', $prefix ?: $path);

$this->anonymousComponentPaths[] = [
'path' => $path,
'prefix' => $prefix,
'prefixHash' => $prefixHash,
];

Container::getInstance()
->make(ViewFactory::class)
->addNamespace($prefixHash, $path);
}








public function anonymousComponentNamespace(string $directory, ?string $prefix = null)
{
$prefix ??= $directory;

$this->anonymousComponentNamespaces[$prefix] = (new Stringable($directory))
->replace('/', '.')
->trim('. ')
->toString();
}








public function componentNamespace($namespace, $prefix)
{
$this->classComponentNamespaces[$prefix] = $namespace;
}






public function getAnonymousComponentPaths()
{
return $this->anonymousComponentPaths;
}






public function getAnonymousComponentNamespaces()
{
return $this->anonymousComponentNamespaces;
}






public function getClassComponentNamespaces()
{
return $this->classComponentNamespaces;
}








public function aliasComponent($path, $alias = null)
{
$alias = $alias ?: Arr::last(explode('.', $path));

$this->directive($alias, function ($expression) use ($path) {
return $expression
? "<?php \$__env->startComponent('{$path}', {$expression}); ?>"
: "<?php \$__env->startComponent('{$path}'); ?>";
});

$this->directive('end'.$alias, function ($expression) {
return '<?php echo $__env->renderComponent(); ?>';
});
}








public function include($path, $alias = null)
{
$this->aliasInclude($path, $alias);
}








public function aliasInclude($path, $alias = null)
{
$alias = $alias ?: Arr::last(explode('.', $path));

$this->directive($alias, function ($expression) use ($path) {
$expression = $this->stripParentheses($expression) ?: '[]';

return "<?php echo \$__env->make('{$path}', {$expression}, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>";
});
}










public function bindDirective($name, callable $handler)
{
$this->directive($name, $handler, bind: true);
}











public function directive($name, callable $handler, bool $bind = false)
{
if (! preg_match('/^\w+(?:::\w+)?$/x', $name)) {
throw new InvalidArgumentException("The directive name [{$name}] is not valid. Directive names must only contain alphanumeric characters and underscores.");
}

$this->customDirectives[$name] = $bind ? $handler->bindTo($this, BladeCompiler::class) : $handler;
}






public function getCustomDirectives()
{
return $this->customDirectives;
}







public function prepareStringsForCompilationUsing(callable $callback)
{
$this->prepareStringsForCompilationUsing[] = $callback;

return $this;
}







public function precompiler(callable $precompiler)
{
$this->precompilers[] = $precompiler;
}








public function usingEchoFormat($format, callable $callback)
{
$originalEchoFormat = $this->echoFormat;

$this->setEchoFormat($format);

try {
$output = call_user_func($callback);
} finally {
$this->setEchoFormat($originalEchoFormat);
}

return $output;
}







public function setEchoFormat($format)
{
$this->echoFormat = $format;
}






public function withDoubleEncoding()
{
$this->setEchoFormat('e(%s, true)');
}






public function withoutDoubleEncoding()
{
$this->setEchoFormat('e(%s, false)');
}






public function withoutComponentTags()
{
$this->compilesComponentTags = false;
}
}
