<?php

namespace Laravel\Prompts;

use Closure;
use Illuminate\Support\Collection;

if (! function_exists('\Laravel\Prompts\text')) {



function text(
string $label,
string $placeholder = '',
string $default = '',
bool|string $required = false,
mixed $validate = null,
string $hint = '',
?Closure $transform = null,
): string {
return (new TextPrompt(...get_defined_vars()))->prompt();
}
}

if (! function_exists('\Laravel\Prompts\textarea')) {



function textarea(
string $label,
string $placeholder = '',
string $default = '',
bool|string $required = false,
mixed $validate = null,
string $hint = '',
int $rows = 5,
?Closure $transform = null,
): string {
return (new TextareaPrompt(...get_defined_vars()))->prompt();
}
}

if (! function_exists('\Laravel\Prompts\password')) {



function password(
string $label,
string $placeholder = '',
bool|string $required = false,
mixed $validate = null,
string $hint = '',
?Closure $transform = null,
): string {
return (new PasswordPrompt(...get_defined_vars()))->prompt();
}
}

if (! function_exists('\Laravel\Prompts\select')) {






function select(
string $label,
array|Collection $options,
int|string|null $default = null,
int $scroll = 5,
mixed $validate = null,
string $hint = '',
bool|string $required = true,
?Closure $transform = null,
): int|string {
return (new SelectPrompt(...get_defined_vars()))->prompt();
}
}

if (! function_exists('\Laravel\Prompts\multiselect')) {







function multiselect(
string $label,
array|Collection $options,
array|Collection $default = [],
int $scroll = 5,
bool|string $required = false,
mixed $validate = null,
string $hint = 'Use the space bar to select options.',
?Closure $transform = null,
): array {
return (new MultiSelectPrompt(...get_defined_vars()))->prompt();
}
}

if (! function_exists('\Laravel\Prompts\confirm')) {



function confirm(
string $label,
bool $default = true,
string $yes = 'Yes',
string $no = 'No',
bool|string $required = false,
mixed $validate = null,
string $hint = '',
?Closure $transform = null,
): bool {
return (new ConfirmPrompt(...get_defined_vars()))->prompt();
}
}

if (! function_exists('\Laravel\Prompts\pause')) {



function pause(string $message = 'Press enter to continue...'): bool
{
return (new PausePrompt(...get_defined_vars()))->prompt();
}
}

if (! function_exists('\Laravel\Prompts\clear')) {



function clear(): void
{
(new Clear)->display();
}
}

if (! function_exists('\Laravel\Prompts\suggest')) {





function suggest(
string $label,
array|Collection|Closure $options,
string $placeholder = '',
string $default = '',
int $scroll = 5,
bool|string $required = false,
mixed $validate = null,
string $hint = '',
?Closure $transform = null,
): string {
return (new SuggestPrompt(...get_defined_vars()))->prompt();
}
}

if (! function_exists('\Laravel\Prompts\search')) {






function search(
string $label,
Closure $options,
string $placeholder = '',
int $scroll = 5,
mixed $validate = null,
string $hint = '',
bool|string $required = true,
?Closure $transform = null,
): int|string {
return (new SearchPrompt(...get_defined_vars()))->prompt();
}
}

if (! function_exists('\Laravel\Prompts\multisearch')) {






function multisearch(
string $label,
Closure $options,
string $placeholder = '',
int $scroll = 5,
bool|string $required = false,
mixed $validate = null,
string $hint = 'Use the space bar to select options.',
?Closure $transform = null,
): array {
return (new MultiSearchPrompt(...get_defined_vars()))->prompt();
}
}

if (! function_exists('\Laravel\Prompts\spin')) {
/**
@template





*/
function spin(Closure $callback, string $message = ''): mixed
{
return (new Spinner($message))->spin($callback);
}
}

if (! function_exists('\Laravel\Prompts\note')) {



function note(string $message, ?string $type = null): void
{
(new Note($message, $type))->display();
}
}

if (! function_exists('\Laravel\Prompts\error')) {



function error(string $message): void
{
(new Note($message, 'error'))->display();
}
}

if (! function_exists('\Laravel\Prompts\warning')) {



function warning(string $message): void
{
(new Note($message, 'warning'))->display();
}
}

if (! function_exists('\Laravel\Prompts\alert')) {



function alert(string $message): void
{
(new Note($message, 'alert'))->display();
}
}

if (! function_exists('\Laravel\Prompts\info')) {



function info(string $message): void
{
(new Note($message, 'info'))->display();
}
}

if (! function_exists('\Laravel\Prompts\intro')) {



function intro(string $message): void
{
(new Note($message, 'intro'))->display();
}
}

if (! function_exists('\Laravel\Prompts\outro')) {



function outro(string $message): void
{
(new Note($message, 'outro'))->display();
}
}

if (! function_exists('\Laravel\Prompts\table')) {






function table(array|Collection $headers = [], array|Collection|null $rows = null): void
{
(new Table($headers, $rows))->display();
}
}

if (! function_exists('\Laravel\Prompts\progress')) {
/**
@template
@template






*/
function progress(
string $label,
iterable|int $steps,
?Closure $callback = null,
string $hint = '',
): array|Progress {
$progress = new Progress($label, $steps, $hint);

if ($callback !== null) {
return $progress->map($callback);
}

return $progress;
}
}

if (! function_exists('\Laravel\Prompts\form')) {
function form(): FormBuilder
{
return new FormBuilder;
}
}
