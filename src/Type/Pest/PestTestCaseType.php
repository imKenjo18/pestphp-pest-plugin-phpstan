<?php

declare(strict_types=1);

namespace Pest\PHPStan\Type\Pest;

use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPUnit\Framework\TestCase;

final class PestTestCaseType
{
    public function __construct(
        private readonly PestConfigReader $pestConfigReader,
        private readonly ReflectionProvider $reflectionProvider,
    ) {}

    public function resolve(string $filePath): Type
    {
        $bindings = $this->pestConfigReader->resolveFileBindings($filePath);

        if ($bindings === []) {
            $bindings = $this->pestConfigReader->resolveBindings($filePath);
        }

        $classNames = [];
        $traitNames = [];

        foreach ($bindings as $binding) {
            if (! $this->reflectionProvider->hasClass($binding)) {
                continue;
            }

            $reflection = $this->reflectionProvider->getClass($binding);

            if ($reflection->isTrait()) {
                $traitNames[] = $binding;

                continue;
            }

            $classNames[] = $binding;
        }

        if ($classNames === []) {
            $classNames[] = TestCase::class;
        }

        $classType = $this->toObjectType($classNames);

        if ($traitNames === []) {
            return $classType;
        }

        return new PestTestCaseWithTraitsType(
            $classNames[0],
            $traitNames,
            $this->reflectionProvider,
        );
    }

    /**
     * @param  list<string>  $classNames
     */
    private function toObjectType(array $classNames): Type
    {
        if (count($classNames) === 1) {
            return new ObjectType($classNames[0]);
        }

        return TypeCombinator::intersect(...array_map(
            static fn (string $className): ObjectType => new ObjectType($className),
            $classNames,
        ));
    }
}
