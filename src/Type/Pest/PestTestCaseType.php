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
        $types = $this->toClassObjectTypes(
            $this->pestConfigReader->resolveFileBindings($filePath),
        );

        if ($types === []) {
            $types = $this->toClassObjectTypes(
                $this->pestConfigReader->resolveBindings($filePath),
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
