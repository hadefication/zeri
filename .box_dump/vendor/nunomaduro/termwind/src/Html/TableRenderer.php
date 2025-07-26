<?php

declare(strict_types=1);

namespace Termwind\Html;

use Iterator;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Termwind\Components\Element;
use Termwind\HtmlRenderer;
use Termwind\Termwind;
use Termwind\ValueObjects\Node;
use Termwind\ValueObjects\Styles;




final class TableRenderer
{



private Table $table;




private BufferedOutput $output;

public function __construct()
{
$this->output = new BufferedOutput(

OutputInterface::VERBOSITY_NORMAL | OutputInterface::OUTPUT_RAW,
true
);

$this->table = new Table($this->output);
}




public function toElement(Node $node): Element
{
$this->parseTable($node);
$this->table->render();

$content = preg_replace('/\n$/', '', $this->output->fetch()) ?? '';

return Termwind::div($content, '', [
'isFirstChild' => $node->isFirstChild(),
]);
}




private function parseTable(Node $node): void
{
$style = $node->getAttribute('style');
if ($style !== '') {
$this->table->setStyle($style);
}

foreach ($node->getChildNodes() as $child) {
match ($child->getName()) {
'thead' => $this->parseHeader($child),
'tfoot' => $this->parseFoot($child),
'tbody' => $this->parseBody($child),
default => $this->parseRows($child)
};
}
}




private function parseHeader(Node $node): void
{
$title = $node->getAttribute('title');

if ($title !== '') {
$this->table->getStyle()->setHeaderTitleFormat(
$this->parseTitleStyle($node)
);
$this->table->setHeaderTitle($title);
}

foreach ($node->getChildNodes() as $child) {
if ($child->isName('tr')) {
foreach ($this->parseRow($child) as $row) {
if (! is_array($row)) {
continue;
}
$this->table->setHeaders($row);
}
}
}
}




private function parseFoot(Node $node): void
{
$title = $node->getAttribute('title');

if ($title !== '') {
$this->table->getStyle()->setFooterTitleFormat(
$this->parseTitleStyle($node)
);
$this->table->setFooterTitle($title);
}

foreach ($node->getChildNodes() as $child) {
if ($child->isName('tr')) {
$rows = iterator_to_array($this->parseRow($child));
if (count($rows) > 0) {
$this->table->addRow(new TableSeparator);
$this->table->addRows($rows);
}
}
}
}




private function parseBody(Node $node): void
{
foreach ($node->getChildNodes() as $child) {
if ($child->isName('tr')) {
$this->parseRows($child);
}
}
}




private function parseRows(Node $node): void
{
foreach ($this->parseRow($node) as $row) {
$this->table->addRow($row);
}
}






private function parseRow(Node $node): Iterator
{
$row = [];

foreach ($node->getChildNodes() as $child) {
if ($child->isName('th') || $child->isName('td')) {
$align = $child->getAttribute('align');

$class = $child->getClassAttribute();

if ($child->isName('th')) {
$class .= ' strong';
}

$text = (string) (new HtmlRenderer)->parse(
trim(preg_replace('/<br\s?+\/?>/', "\n", $child->getHtml()) ?? '')
);

if ((bool) preg_match(Styles::STYLING_REGEX, $text)) {
$class .= ' font-normal';
}

$row[] = new TableCell(


(string) Termwind::span($text, $class),
[

'colspan' => max((int) $child->getAttribute('colspan'), 1),
'rowspan' => max((int) $child->getAttribute('rowspan'), 1),


'style' => $this->parseCellStyle(
$class,
$align === '' ? TableCellStyle::DEFAULT_ALIGN : $align
),
]
);
}
}

if ($row !== []) {
yield $row;
}

$border = (int) $node->getAttribute('border');
for ($i = $border; $i--; $i > 0) {
yield new TableSeparator;
}
}




private function parseCellStyle(string $styles, string $align = TableCellStyle::DEFAULT_ALIGN): TableCellStyle
{


$element = Termwind::span('%s', $styles);

$styles = [];

$colors = $element->getProperties()['colors'] ?? [];

foreach ($colors as $option => $content) {
if (in_array($option, ['fg', 'bg'], true)) {
$content = is_array($content) ? array_pop($content) : $content;

$styles[] = "$option=$content";
}
}


if ($styles === []) {
$cellFormat = '%s';
} else {
$cellFormat = '<'.implode(';', $styles).'>%s</>';
}

return new TableCellStyle([
'align' => $align,
'cellFormat' => $cellFormat,
]);
}




private function parseTitleStyle(Node $node): string
{
return (string) Termwind::span(' %s ', $node->getClassAttribute());
}
}
