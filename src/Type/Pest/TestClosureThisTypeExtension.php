<?php

declare(strict_types=1);

namespace Pest\PHPStan\Type\Pest;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\ParameterReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\FunctionParameterClosureThisExtension;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPUnit\Framework\TestCase;

final class TestClosureThisTypeExtension implements FunctionParameterClosureThisExtension
{
    private const PEST_TEST_FUNCTIONS = [
        'it',
        'test',
        'describe',
    ];

    private const PEST_HOOK_FUNCTIONS = [
        'beforeEach',
        'afterEach',
        'beforeAll',
        'afterAll',
    ];

    public function __construct(
        private readonly PestConfigReader $pestConfigReader,
        private readonly ReflectionProvider $reflectionProvider,
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
        $types = $this->toClassObjectTypes(
            $this->pestConfigReader->resolveFileBindings($scope->getFile()),
        );

        if ($types === []) {
            $types = $this->toClassObjectTypes(
                $this->pestConfigReader->resolveBindings($scope->getFile()),
            );
        }

        if ($types === []) {
            return new ObjectType(TestCase::class);
        }

        return count($types) === 1 ? $types[0] : TypeCombinator::intersect(...$types);
    }

    /**
     * @param  list<string>  $bindings
     * @return list<ObjectType>
     */
    private function toClassObjectTypes(array $bindings): array
    {
        $types = [];

        foreach ($bindings as $binding) {
            if (! $this->reflectionProvider->hasClass($binding)) {
                continue;
            }

            if ($this->reflectionProvider->getClass($binding)->isTrait()) {
                continue;
            }

            $types[] = new ObjectType($binding);
        }

        return $types;
    }
}
