<?php

declare(strict_types=1);

namespace Pest\PHPStan\Rules;

use Pest\PHPStan\ClosureBodyNodeFinder;
use Pest\PHPStan\Diagnostics\PestDiagnosticIdentifiers;
use Pest\PHPStan\PestFunctionDetector;
use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<FuncCall>
 */
final class DisallowedCallInDescribeRule implements Rule
{
    /** @var array<string, string> */
    private const FORBIDDEN_FUNCTIONS = [
        'beforeAll' => PestDiagnosticIdentifiers::DESCRIBE_BEFORE_ALL_DISALLOWED,
        'afterAll' => PestDiagnosticIdentifiers::DESCRIBE_AFTER_ALL_DISALLOWED,
    ];

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

        $forbiddenCalls = ClosureBodyNodeFinder::find(
            $closure,
            static fn (Node $node): bool => $node instanceof FuncCall
                && $node->name instanceof Name
                && isset(self::FORBIDDEN_FUNCTIONS[$node->name->toString()]),
        );

        $errors = [];

        foreach ($forbiddenCalls as $call) {
            if (! $call instanceof FuncCall) {
                continue;
            }

            if (! $call->name instanceof Name) {
                continue;
            }

            $functionName = $call->name->toString();

            $errors[] = RuleErrorBuilder::message(
                sprintf('%s() cannot be used inside describe() blocks. Use %s instead.', $functionName, $functionName === 'beforeAll' ? 'beforeEach()' : 'afterEach()')
            )
                ->identifier(self::FORBIDDEN_FUNCTIONS[$functionName])
                ->line($call->getStartLine())
                ->build();
        }

        return $errors;
    }
}
