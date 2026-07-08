<?php

declare(strict_types=1);

namespace Pest\PHPStan\Rules;

use Pest\PHPStan\Diagnostics\PestDiagnosticIdentifiers;
use Pest\PHPStan\PestFunctionDetector;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<FuncCall>
 */
final class StaticTestClosureRule implements Rule
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
        $name = PestFunctionDetector::getFunctionName($node);
        if ($name === null) {
            return [];
        }

        $closure = PestFunctionDetector::extractClosure($node);
        if ($closure === null) {
            return [];
        }

        if (! $closure->static) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                sprintf('Test closure passed to %s() must not be static. Remove the `static` keyword.', $name)
            )
                ->identifier(PestDiagnosticIdentifiers::TEST_STATIC_CLOSURE)
                ->build(),
        ];
    }
}
