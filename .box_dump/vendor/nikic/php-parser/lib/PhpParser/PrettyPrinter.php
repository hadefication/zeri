<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Node\Expr;

interface PrettyPrinter {







public function prettyPrint(array $stmts): string;








public function prettyPrintExpr(Expr $node): string;








public function prettyPrintFile(array $stmts): string;
















public function printFormatPreserving(array $stmts, array $origStmts, array $origTokens): string;
}
