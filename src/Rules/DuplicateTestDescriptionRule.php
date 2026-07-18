<?php

declare(strict_types=1);

namespace Pest\PHPStan\Rules;

use Pest\PHPStan\Diagnostics\PestDiagnosticIdentifiers;
use Pest\PHPStan\PestFunctionDetector;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Namespace_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\FileNode;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<FileNode>
 */
final class DuplicateTestDescriptionRule implements Rule
{
    public function getNodeType(): string
    {
        return FileNode::class;
    }

    /**
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $errors = [];
        $seen = [];

        foreach ($this->topLevelTestCalls($node->getNodes()) as $call) {
            $description = PestFunctionDetector::extractDescription($call);
            if ($description === null) {
                continue;
            }

            if ($call->name instanceof Name && $call->name->toString() === 'it') {
                $description = 'it '.$description;
            }

            if (isset($seen[$description])) {
                $errors[] = RuleErrorBuilder::message(
                    sprintf("A test with the description '%s' already exists in this file.", $description)
                )
                    ->identifier(PestDiagnosticIdentifiers::TEST_DUPLICATE_DESCRIPTION)
                    ->line($call->getStartLine())
                    ->build();

                continue;
            }

            $seen[$description] = true;
        }

        return $errors;
    }

    /**
     * @param  Node[]  $nodes
     * @return list<FuncCall>
     */
    private function topLevelTestCalls(array $nodes): array
    {
        $calls = [];

        foreach ($nodes as $node) {
            if ($node instanceof Namespace_) {
                array_push($calls, ...$this->topLevelTestCalls($node->stmts));

                continue;
            }

            if (! $node instanceof Expression) {
                continue;
            }

            $call = $this->rootFuncCall($node->expr);
            if ($call instanceof FuncCall && PestFunctionDetector::isTestFunction($call)) {
                $calls[] = $call;
            }
        }

        return $calls;
    }

    private function rootFuncCall(Expr $expr): ?FuncCall
    {
        while ($expr instanceof MethodCall) {
            $expr = $expr->var;
        }

        return $expr instanceof FuncCall ? $expr : null;
    }
}
