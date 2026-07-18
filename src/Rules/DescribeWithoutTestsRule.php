<?php

declare(strict_types=1);

namespace Pest\PHPStan\Rules;

use Pest\PHPStan\ClosureBodyNodeFinder;
use Pest\PHPStan\Diagnostics\PestDiagnosticIdentifiers;
use Pest\PHPStan\PestFunctionDetector;
use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Nop;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<FuncCall>
 */
final class DescribeWithoutTestsRule implements Rule
{
    public function getNodeType(): string
    {
        return FuncCall::class;
    }

    /**
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! PestFunctionDetector::isDescribeFunction($node)) {
            return [];
        }

        $closure = PestFunctionDetector::extractClosure($node);
        if (! $closure instanceof Closure) {
            return [];
        }

        $realStmts = array_filter(
            $closure->stmts,
            static fn (Node $stmt): bool => ! $stmt instanceof Nop
        );

        if ($realStmts !== [] && $this->containsTestCall($closure)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                sprintf("describe() block '%s' contains no tests.", $this->extractDescribeDescription($node))
            )
                ->identifier(PestDiagnosticIdentifiers::DESCRIBE_WITHOUT_TESTS)
                ->build(),
        ];
    }

    private function containsTestCall(Closure $closure): bool
    {
        $testCalls = ClosureBodyNodeFinder::find(
            $closure,
            static fn (Node $node): bool => $node instanceof FuncCall
                && (PestFunctionDetector::isTestFunction($node) || PestFunctionDetector::isDescribeFunction($node)),
        );

        return $testCalls !== [];
    }

    private function extractDescribeDescription(FuncCall $node): string
    {
        $args = $node->getArgs();
        if ($args === []) {
            return '';
        }

        $firstArg = $args[0]->value;
        if ($firstArg instanceof String_) {
            return $firstArg->value;
        }

        return '';
    }
}
