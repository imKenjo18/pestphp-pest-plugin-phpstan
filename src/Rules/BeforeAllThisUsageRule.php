<?php

declare(strict_types=1);

namespace Pest\PHPStan\Rules;

use Pest\PHPStan\ClosureBodyNodeFinder;
use Pest\PHPStan\Diagnostics\PestDiagnosticIdentifiers;
use Pest\PHPStan\Diagnostics\PestDiagnostics;
use Pest\PHPStan\PestFunctionDetector;
use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;

/**
 * @implements Rule<FuncCall>
 */
final class BeforeAllThisUsageRule implements Rule
{
    /** @var array<string, array{identifier: string, replacement: string}> */
    private const array STATIC_HOOKS = [
        'beforeAll' => [
            'identifier' => PestDiagnosticIdentifiers::LIFECYCLE_BEFORE_ALL_THIS_USAGE,
            'replacement' => 'beforeEach',
        ],
        'afterAll' => [
            'identifier' => PestDiagnosticIdentifiers::LIFECYCLE_AFTER_ALL_THIS_USAGE,
            'replacement' => 'afterEach',
        ],
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
        if (! $node->name instanceof Name) {
            return [];
        }

        $functionName = $node->name->toString();
        if (! isset(self::STATIC_HOOKS[$functionName])) {
            return [];
        }

        $closure = PestFunctionDetector::extractClosure($node);
        if (! $closure instanceof Closure) {
            return [];
        }

        $thisUsages = ClosureBodyNodeFinder::find(
            $closure,
            static fn (Node $node): bool => $node instanceof Variable && $node->name === 'this',
        );

        $errors = [];
        foreach ($thisUsages as $usage) {
            $errors[] = $this->buildStaticHookError($functionName, $usage->getStartLine());
        }

        return $errors;
    }

    private function buildStaticHookError(string $functionName, int $line): IdentifierRuleError
    {
        $hookConfig = self::STATIC_HOOKS[$functionName];

        return PestDiagnostics::toRuleError(
            PestDiagnostics::invalidLifecycleThisUsage(
                $functionName,
                $hookConfig['replacement'],
                $hookConfig['identifier'],
                $line,
            )
        );
    }
}
