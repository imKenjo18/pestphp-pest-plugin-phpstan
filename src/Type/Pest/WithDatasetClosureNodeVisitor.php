<?php

declare(strict_types=1);

namespace Pest\PHPStan\Type\Pest;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Closure as ClosureExpr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeFinder;
use PhpParser\NodeVisitorAbstract;

final class WithDatasetClosureNodeVisitor extends NodeVisitorAbstract
{
    private const array PEST_TEST_FUNCTIONS = ['test', 'it'];

    public function enterNode(Node $node): ?Node
    {
        if (! $node instanceof MethodCall) {
            return null;
        }

        if (! $node->name instanceof Identifier || $node->name->name !== 'with') {
            return null;
        }

        if (! $this->isPestTestChain($node)) {
            return null;
        }

        foreach ($node->getArgs() as $arg) {
            $arg->value = $this->wrapInClosure($arg->value);
        }

        return null;
    }

    private function wrapInClosure(Expr $node): Expr
    {
        if ($node instanceof ClosureExpr || $node instanceof ArrowFunction) {
            return $node;
        }

        if (! $this->containsClosure($node)) {
            return $node;
        }

        return new ClosureExpr(
            [
                'stmts' => [
                    new Return_($node),
                ],
            ],
            $node->getAttributes(),
        );
    }

    private function containsClosure(Expr $node): bool
    {
        $nodeFinder = new NodeFinder;

        return $nodeFinder->findFirst(
            [$node],
            static fn (Node $n): bool => $n instanceof ClosureExpr || $n instanceof ArrowFunction,
        ) instanceof Node;
    }

    private function isPestTestChain(MethodCall $methodCall): bool
    {
        $root = $methodCall->var;

        while ($root instanceof MethodCall) {
            $root = $root->var;
        }

        if (! $root instanceof FuncCall || ! $root->name instanceof Name) {
            return false;
        }

        return in_array($root->name->getLast(), self::PEST_TEST_FUNCTIONS, true);
    }
}
