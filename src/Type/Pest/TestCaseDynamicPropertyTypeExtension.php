<?php

declare(strict_types=1);

namespace Pest\PHPStan\Type\Pest;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\ErrorType;
use PHPStan\Type\ExpressionTypeResolverExtension;
use PHPStan\Type\MixedType;
use PHPStan\Type\NeverType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPUnit\Framework\TestCase;

final class TestCaseDynamicPropertyTypeExtension implements ExpressionTypeResolverExtension
{
    /**
     * @var array<string, true>
     */
    private array $resolving = [];

    public function __construct(
        private readonly PestHookPropertyReader $hookPropertyReader,
    ) {}

    public function getType(Expr $expr, Scope $scope): ?Type
    {
        if (! $expr instanceof PropertyFetch) {
            return null;
        }

        if (! $expr->name instanceof Identifier) {
            return null;
        }

        $varType = $scope->getType($expr->var);
        $testCaseType = new ObjectType(TestCase::class);

        if (! $testCaseType->isSuperTypeOf($varType)->yes()) {
            return null;
        }

        $propertyName = $expr->name->name;

        if ($this->hasNativeProperty($varType, $propertyName)) {
            return null;
        }

        $propertyExprs = $this->hookPropertyReader->getPropertyExprs($scope->getFile());

        if (! isset($propertyExprs[$propertyName])) {
            return null;
        }

        $key = $scope->getFile().'::'.$propertyName;

        if (isset($this->resolving[$key])) {
            return null;
        }

        $this->resolving[$key] = true;

        try {
            $types = [];
            foreach ($propertyExprs[$propertyName] as $rhsExpr) {
                $resolved = $scope->getType($rhsExpr);
                if ($resolved instanceof ErrorType) {
                    continue;
                }

                if ($resolved instanceof NeverType) {
                    continue;
                }

                if ($resolved instanceof MixedType) {
                    continue;
                }

                $types[] = $resolved;
            }
        } finally {
            unset($this->resolving[$key]);
        }

        if ($types === []) {
            return null;
        }

        return TypeCombinator::union(...$types);
    }

    private function hasNativeProperty(Type $type, string $propertyName): bool
    {
        return array_any($type->getObjectClassReflections(), fn (ClassReflection $classReflection): bool => $classReflection->hasNativeProperty($propertyName));
    }
}
