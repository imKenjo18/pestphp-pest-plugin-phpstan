<?php

declare(strict_types=1);

namespace Pest\PHPStan\Type\Pest;

use Pest\Expectation;
use Pest\Expectations\OppositeExpectation;
use Pest\Mixins\Expectation as MixinsExpectation;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Type\Type;

final class OppositeExpectationMethodReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return OppositeExpectation::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getDeclaringClass()->getName() === MixinsExpectation::class;
    }

    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope
    ): Type {
        $valueType = $scope->getType($methodCall->var)
            ->getTemplateType(OppositeExpectation::class, 'TValue');

        return new GenericObjectType(Expectation::class, [$valueType]);
    }
}
