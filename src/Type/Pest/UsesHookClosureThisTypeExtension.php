<?php

declare(strict_types=1);

namespace Pest\PHPStan\Type\Pest;

use Pest\PendingCalls\UsesCall;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParameterReflection;
use PHPStan\Type\MethodParameterClosureThisExtension;
use PHPStan\Type\Type;

final class UsesHookClosureThisTypeExtension implements MethodParameterClosureThisExtension
{
    // @note: beforeAll/afterAll are absent on purpose — Testable chains them through ChainableClosure::boundStatically(), which binds null, so $this does not exist in them at runtime.
    private const array PEST_EACH_HOOK_METHODS = [
        'beforeeach',
        'aftereach',
    ];

    public function __construct(
        private readonly PestTestCaseType $pestTestCaseType,
        private readonly PestConfigReader $pestConfigReader,
    ) {}

    public function isMethodSupported(MethodReflection $methodReflection, ParameterReflection $parameter): bool
    {
        return in_array(mb_strtolower($methodReflection->getName()), self::PEST_EACH_HOOK_METHODS, true)
            && $methodReflection->getDeclaringClass()->is(UsesCall::class);
    }

    public function getClosureThisTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        ParameterReflection $parameter,
        Scope $scope
    ): Type {
        // @note: the bound class comes from the chain itself, because a Pest.php config file is not covered by its own in() targets.
        $type = $this->pestTestCaseType->resolveFromBindings(
            $this->pestConfigReader->resolveChainBindings($methodCall->var),
        );

        return $type ?? $this->pestTestCaseType->resolve($scope->getFile());
    }
}
