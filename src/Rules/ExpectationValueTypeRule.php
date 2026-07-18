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
final class ExpectationValueTypeRule implements Rule
{
    public function __construct(
        private readonly ExpectationSemanticAnalyzer $semanticAnalyzer,
    ) {}

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $diagnostic = $this->semanticAnalyzer->analyzeInvalidMatcherType($node, $scope);
        if (! $diagnostic instanceof PestDiagnostic) {
            return [];
        }

        return [PestDiagnostics::toRuleError($diagnostic)];
    }
}
