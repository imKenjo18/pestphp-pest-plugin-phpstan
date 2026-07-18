<?php

declare(strict_types=1);

namespace Pest\PHPStan\Type\Pest;

use Pest\Expectation;
use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\DynamicFunctionReturnTypeExtension;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Type\NullType;
use PHPStan\Type\Type;

final class PestFunctionReturnTypeExtension implements DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return $functionReflection->getName() === 'expect';
    }

    public function getTypeFromFunctionCall(
        FunctionReflection $functionReflection,
        FuncCall $functionCall,
        Scope $scope
    ): Type {
        $args = $functionCall->getArgs();

        if ($args === []) {
            return new GenericObjectType(Expectation::class, [new NullType]);
        }

        $valueType = $scope->getType($args[0]->value);

        return new GenericObjectType(Expectation::class, [$valueType]);
    }
}
