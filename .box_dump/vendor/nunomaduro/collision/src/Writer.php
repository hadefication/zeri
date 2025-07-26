<?php

declare(strict_types=1);

namespace NunoMaduro\Collision;

use Closure;
use NunoMaduro\Collision\Contracts\RenderableOnCollisionEditor;
use NunoMaduro\Collision\Contracts\RenderlessEditor;
use NunoMaduro\Collision\Contracts\RenderlessTrace;
use NunoMaduro\Collision\Contracts\SolutionsRepository;
use NunoMaduro\Collision\Exceptions\TestException;
use NunoMaduro\Collision\SolutionsRepositories\NullSolutionsRepository;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use Whoops\Exception\Frame;
use Whoops\Exception\Inspector;






final class Writer
{



public const VERBOSITY_NORMAL_FRAMES = 1;




private SolutionsRepository $solutionsRepository;




private OutputInterface $output;




private ArgumentFormatter $argumentFormatter;




private Highlighter $highlighter;







private array $ignore = [];




private bool $showTrace = true;




private bool $showTitle = true;




private bool $showEditor = true;




public function __construct(
?SolutionsRepository $solutionsRepository = null,
?OutputInterface $output = null,
?ArgumentFormatter $argumentFormatter = null,
?Highlighter $highlighter = null
) {
$this->solutionsRepository = $solutionsRepository ?: new NullSolutionsRepository;
$this->output = $output ?: new ConsoleOutput;
$this->argumentFormatter = $argumentFormatter ?: new ArgumentFormatter;
$this->highlighter = $highlighter ?: new Highlighter;
}

public function write(Inspector $inspector): void
{
$this->renderTitleAndDescription($inspector);

$frames = $this->getFrames($inspector);

$exception = $inspector->getException();

if ($exception instanceof RenderableOnCollisionEditor) {
$editorFrame = $exception->toCollisionEditor();
} else {
$editorFrame = array_shift($frames);
}

if ($this->showEditor
&& $editorFrame !== null
&& ! $exception instanceof RenderlessEditor
) {
$this->renderEditor($editorFrame);
}

$this->renderSolution($inspector);

if ($this->showTrace && ! empty($frames) && ! $exception instanceof RenderlessTrace) {
$this->renderTrace($frames);
} elseif (! $exception instanceof RenderlessEditor) {
$this->output->writeln('');
}
}

public function ignoreFilesIn(array $ignore): self
{
$this->ignore = $ignore;

return $this;
}

public function showTrace(bool $show): self
{
$this->showTrace = $show;

return $this;
}

public function showTitle(bool $show): self
{
$this->showTitle = $show;

return $this;
}

public function showEditor(bool $show): self
{
$this->showEditor = $show;

return $this;
}

public function setOutput(OutputInterface $output): self
{
$this->output = $output;

return $this;
}

public function getOutput(): OutputInterface
{
return $this->output;
}






private function getFrames(Inspector $inspector): array
{
return $inspector->getFrames()
->filter(
function ($frame) {


if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
return true;
}

foreach ($this->ignore as $ignore) {
if (is_string($ignore)) {

$sanitizedPath = (string) str_replace('\\', '/', $frame->getFile());
if (preg_match($ignore, $sanitizedPath)) {
return false;
}
}

if ($ignore instanceof Closure) {
if ($ignore($frame)) {
return false;
}
}
}

return true;
}
)
->getArray();
}




private function renderTitleAndDescription(Inspector $inspector): self
{

$exception = $inspector->getException();
$message = rtrim($exception->getMessage());
$class = $exception instanceof TestException
? $exception->getClassName()
: $inspector->getExceptionName();

if ($this->showTitle) {
$this->render("<bg=red;options=bold> $class </>");
$this->output->writeln('');
}

$this->output->writeln("<fg=default;options=bold>  $message</>");

return $this;
}




private function renderSolution(Inspector $inspector): self
{
$throwable = $inspector->getException();

$solutions = $throwable instanceof Throwable
? $this->solutionsRepository->getFromThrowable($throwable)
: [];

foreach ($solutions as $solution) {

$title = $solution->getSolutionTitle(); 
$description = $solution->getSolutionDescription(); 
$links = $solution->getDocumentationLinks(); 

$description = trim((string) preg_replace("/\n/", "\n    ", $description));

$this->render(sprintf(
'<fg=cyan;options=bold>i</>   <fg=default;options=bold>%s</>: %s %s',
rtrim($title, '.'),
$description,
implode(', ', array_map(function (string $link) {
return sprintf("\n      <fg=gray>%s</>", $link);
}, $links))
));
}

return $this;
}





private function renderEditor(Frame $frame): self
{
if ($frame->getFile() !== 'Unknown') {
$file = $this->getFileRelativePath((string) $frame->getFile());


$line = (int) $frame->getLine();
$this->render('at <fg=green>'.$file.'</>'.':<fg=green>'.$line.'</>');

$content = $this->highlighter->highlight((string) $frame->getFileContents(), (int) $frame->getLine());

$this->output->writeln($content);
}

return $this;
}




private function renderTrace(array $frames): self
{
$vendorFrames = 0;
$userFrames = 0;

if (! empty($frames)) {
$this->output->writeln(['']);
}

foreach ($frames as $i => $frame) {
if ($this->output->getVerbosity() < OutputInterface::VERBOSITY_VERBOSE && strpos($frame->getFile(), '/vendor/') !== false) {
$vendorFrames++;

continue;
}

if ($userFrames > self::VERBOSITY_NORMAL_FRAMES && $this->output->getVerbosity() < OutputInterface::VERBOSITY_VERBOSE) {
break;
}

$userFrames++;

$file = $this->getFileRelativePath($frame->getFile());
$line = $frame->getLine();
$class = empty($frame->getClass()) ? '' : $frame->getClass().'::';
$function = $frame->getFunction();
$args = $this->argumentFormatter->format($frame->getArgs());
$pos = str_pad((string) ((int) $i + 1), 4, ' ');

if ($vendorFrames > 0) {
$this->output->writeln(
sprintf("      \e[2m+%s vendor frames \e[22m", $vendorFrames)
);
$vendorFrames = 0;
}

$this->render("<fg=yellow>$pos</><fg=default;options=bold>$file</>:<fg=default;options=bold>$line</>", (bool) $class && $i > 0);
if ($class) {
$this->render("<fg=gray>    $class$function($args)</>", false);
}
}

if (! empty($frames)) {
$this->output->writeln(['']);
}

return $this;
}




private function render(string $message, bool $break = true): self
{
if ($break) {
$this->output->writeln('');
}

$this->output->writeln("  $message");

return $this;
}




private function getFileRelativePath(string $filePath): string
{
$cwd = (string) getcwd();

if (! empty($cwd)) {
return str_replace("$cwd".DIRECTORY_SEPARATOR, '', $filePath);
}

return $filePath;
}
}
