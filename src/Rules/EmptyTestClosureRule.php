<?php

declare(strict_types=1);

namespace Pest\PHPStan\Rules;

use Pest\PHPStan\Diagnostics\PestDiagnosticIdentifiers;
use Pest\PHPStan\PestFunctionDetector;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Nop;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<Expression>
 */
final class EmptyTestClosureRule implements Rule
{
    public function getNodeType(): string
    {
        return Expression::class;
    }

    /**
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($this->chainMarksTestAsPending($node->expr)) {
            return [];
        }

        $call = $this->rootFuncCall($node->expr);
        if (! $call instanceof FuncCall || ! PestFunctionDetector::isTestFunction($call)) {
            return [];
        }

        $closure = PestFunctionDetector::extractClosure($call);
        if (! $closure instanceof Closure) {
            return [];
        }

        $realStmts = array_filter(
            $closure->stmts,
            static fn (Node $stmt): bool => ! $stmt instanceof Nop
        );

        if ($realStmts !== []) {
            return [];
        }

        $description = PestFunctionDetector::extractDescription($call) ?? '';

        return [
            RuleErrorBuilder::message(
                sprintf("Test '%s' has an empty closure body. Add assertions or chain ->todo() to mark as pending.", $description)
            )
                ->identifier(PestDiagnosticIdentifiers::TEST_EMPTY_CLOSURE)
                ->line($call->getStartLine())
                ->build(),
        ];
    }

    private function chainMarksTestAsPending(Expr $expr): bool
    {
        while ($expr instanceof MethodCall) {
            if ($expr->name instanceof Identifier && $expr->name->toString() === 'todo') {
                return true;
            }

            $expr = $expr->var;
        }

        return false;
    }

    private function rootFuncCall(Expr $expr): ?FuncCall
    {
        while ($expr instanceof MethodCall) {
            $expr = $expr->var;
        }

        return $expr instanceof FuncCall ? $expr : null;
    }
}
