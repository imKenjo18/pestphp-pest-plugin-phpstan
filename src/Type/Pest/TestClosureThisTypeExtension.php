<?php

declare(strict_types=1);

namespace Pest\PHPStan\Type\Pest;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\ParameterReflection;
use PHPStan\Type\FunctionParameterClosureThisExtension;
use PHPStan\Type\Type;

final class TestClosureThisTypeExtension implements FunctionParameterClosureThisExtension
{
    private const array PEST_TEST_FUNCTIONS = [
        'it',
        'test',
        'describe',
    ];

    private const array PEST_HOOK_FUNCTIONS = [
        'beforeEach',
        'afterEach',
        'beforeAll',
        'afterAll',
    ];

    public function __construct(
        private readonly PestTestCaseType $pestTestCaseType,
    ) {}

    public function isFunctionSupported(FunctionReflection $functionReflection, ParameterReflection $parameter): bool
    {
        $functionName = $functionReflection->getName();

        return in_array($functionName, self::PEST_TEST_FUNCTIONS, true)
            || in_array($functionName, self::PEST_HOOK_FUNCTIONS, true);
    }

    public function getClosureThisTypeFromFunctionCall(
        FunctionReflection $functionReflection,
        FuncCall $functionCall,
        ParameterReflection $parameter,
        Scope $scope
    ): Type {
        return $this->pestTestCaseType->resolve($scope->getFile());
    }
}
