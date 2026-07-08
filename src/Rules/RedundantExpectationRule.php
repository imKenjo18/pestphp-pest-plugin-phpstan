<?php

declare(strict_types=1);

namespace Pest\PHPStan\Rules;

use Pest\PHPStan\Analysis\Expectation\ExpectationSemanticAnalyzer;
use Pest\PHPStan\Diagnostics\PestDiagnostic;
use Pest\PHPStan\Diagnostics\PestDiagnostics;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;

/**
 * @implements Rule<MethodCall>
 */
final class RedundantExpectationRule implements Rule
{
    private readonly ExpectationSemanticAnalyzer $semanticAnalyzer;

    public function __construct(?ExpectationSemanticAnalyzer $semanticAnalyzer = null)
    {
        $this->semanticAnalyzer = $semanticAnalyzer ?? new ExpectationSemanticAnalyzer;
    }

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $diagnostic = $this->semanticAnalyzer->analyzeRedundantExpectation($node, $scope);
        if (! $diagnostic instanceof PestDiagnostic) {
            return [];
        }

        return [PestDiagnostics::toRuleError($diagnostic)];
    }
}
