<?php

declare(strict_types=1);

namespace Pest\PHPStan\Rules;

use Pest\PHPStan\Diagnostics\PestDiagnosticIdentifiers;
use Pest\PHPStan\Type\Pest\PestConfigReader;
use Pest\PHPStan\Type\Pest\PestFileDiscoverer;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Expression;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<Expression>
 */
final class RedundantLocalUseRule implements Rule
{
    public function __construct(
        private readonly PestConfigReader $pestConfigReader,
        private readonly PestFileDiscoverer $pestFileDiscoverer,
    ) {}

    public function getNodeType(): string
    {
        return Expression::class;
    }

    /**
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($this->pestFileDiscoverer->isPestConfigFile($scope->getFile())) {
            return [];
        }

        $localUseCalls = $this->resolveLocalUseCalls($node->expr);
        if ($localUseCalls === []) {
            return [];
        }

        $globalUses = $this->pestConfigReader->resolveGlobalUses($scope->getFile());
        if ($globalUses === []) {
            return [];
        }

        $globalByClass = [];
        foreach ($globalUses as $globalUse) {
            $globalByClass[mb_strtolower(mb_ltrim($globalUse['class'], '\\'))] = $globalUse['config'];
        }

        $errors = [];
        foreach ($localUseCalls as $localUseCall) {
            foreach ($localUseCall->getArgs() as $arg) {
                $className = $this->resolveClassName($arg->value, $scope);
                if ($className === null) {
                    continue;
                }

                $config = $globalByClass[mb_strtolower(mb_ltrim($className, '\\'))] ?? null;
                if ($config === null) {
                    continue;
                }

                $errors[] = RuleErrorBuilder::message(sprintf(
                    '%s is already applied globally through %s for this test file.',
                    $this->shortName($className),
                    $this->displayConfigPath($config),
                ))
                    ->identifier(PestDiagnosticIdentifiers::CONFIG_REDUNDANT_LOCAL_USE)
                    ->line($arg->getStartLine())
                    ->build();
            }
        }

        return $errors;
    }

    /**
     * @return list<FuncCall|MethodCall>
     */
    private function resolveLocalUseCalls(Expr $expr): array
    {
        if ($this->chainHasIn($expr)) {
            return [];
        }

        $useCalls = [];
        $current = $expr;

        while ($current instanceof MethodCall) {
            if ($current->name instanceof Identifier && in_array(mb_strtolower($current->name->toString()), ['use', 'uses'], true)) {
                $useCalls[] = $current;
            }

            $current = $current->var;
        }

        if (! $current instanceof FuncCall || ! $current->name instanceof Name) {
            return [];
        }

        $rootName = mb_strtolower($current->name->toString());

        if ($rootName === 'uses') {
            return [$current, ...array_reverse($useCalls)];
        }

        if ($rootName === 'pest') {
            return array_reverse($useCalls);
        }

        return [];
    }

    private function chainHasIn(Expr $expr): bool
    {
        while ($expr instanceof MethodCall) {
            if ($expr->name instanceof Identifier && mb_strtolower($expr->name->toString()) === 'in') {
                return true;
            }

            $expr = $expr->var;
        }

        return false;
    }

    private function resolveClassName(Expr $expr, Scope $scope): ?string
    {
        if (! $expr instanceof ClassConstFetch
            || ! $expr->name instanceof Identifier
            || mb_strtolower($expr->name->toString()) !== 'class'
            || ! $expr->class instanceof Name) {
            return null;
        }

        return $scope->resolveName($expr->class);
    }

    private function shortName(string $className): string
    {
        $parts = explode('\\', $className);

        return end($parts);
    }

    private function displayConfigPath(string $config): string
    {
        $workingDirectory = getcwd();
        $normalizedWorkingDirectory = str_replace('\\', '/', $workingDirectory === false ? '' : $workingDirectory);
        if ($normalizedWorkingDirectory !== '' && str_starts_with($config, $normalizedWorkingDirectory.'/')) {
            return mb_substr($config, mb_strlen($normalizedWorkingDirectory) + 1);
        }

        return $config;
    }
}
