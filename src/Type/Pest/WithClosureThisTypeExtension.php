<?php

declare(strict_types=1);

namespace Pest\PHPStan\Type\Pest;

use Pest\PendingCalls\TestCall;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParameterReflection;
use PHPStan\Type\MethodParameterClosureThisExtension;
use PHPStan\Type\Type;

final class WithClosureThisTypeExtension implements MethodParameterClosureThisExtension
{
    public function __construct(
        private readonly PestTestCaseType $pestTestCaseType,
    ) {}

    public function isMethodSupported(MethodReflection $methodReflection, ParameterReflection $parameter): bool
    {
        return mb_strtolower($methodReflection->getName()) === 'with'
            && $methodReflection->getDeclaringClass()->is(TestCall::class);
    }

    public function getClosureThisTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        ParameterReflection $parameter,
        Scope $scope
    ): Type {
        return $this->pestTestCaseType->resolve($scope->getFile());
    }
}
