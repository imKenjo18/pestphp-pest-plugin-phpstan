<?php

declare(strict_types=1);

namespace Pest\PHPStan\Type\Pest;

use Pest\PHPStan\Analysis\Expectation\ExpectationNarrowingResolver;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Equal;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Analyser\SpecifiedTypes;
use PHPStan\Analyser\TypeSpecifier;
use PHPStan\Analyser\TypeSpecifierAwareExtension;
use PHPStan\Analyser\TypeSpecifierContext;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\MethodTypeSpecifyingExtension;
use PHPStan\Type\Type;

final class ExpectationTypeSpecifyingExtension implements MethodTypeSpecifyingExtension, TypeSpecifierAwareExtension
{
    private TypeSpecifier $typeSpecifier;

    /**
     * @param  class-string  $className
     */
    public function __construct(
        private readonly ExpectationNarrowingResolver $narrowingResolver,
        private readonly string $className,
    ) {}

    public function getClass(): string
    {
        return $this->className;
    }

    public function setTypeSpecifier(TypeSpecifier $typeSpecifier): void
    {
        $this->typeSpecifier = $typeSpecifier;
    }

    public function isMethodSupported(MethodReflection $methodReflection, MethodCall $node, TypeSpecifierContext $context): bool
    {
        return $context->null();
    }

    public function specifyTypes(MethodReflection $methodReflection, MethodCall $node, Scope $scope, TypeSpecifierContext $context): SpecifiedTypes
    {
        $specifiedTypes = new SpecifiedTypes;

        foreach ($this->narrowingResolver->resolve($node, $scope) as $narrowing) {
            $context = $narrowing->negated
                ? TypeSpecifierContext::createTruthy()->negate()
                : TypeSpecifierContext::createTruthy();

            if ($narrowing->comparedExpr instanceof Expr) {
                $comparison = $narrowing->loose
                    ? new Equal($narrowing->subject, $narrowing->comparedExpr)
                    : new Identical($narrowing->subject, $narrowing->comparedExpr);

                $specifiedTypes = $specifiedTypes->unionWith(
                    $this->typeSpecifier->specifyTypesInCondition($scope, $comparison, $context),
                );

                continue;
            }

            if (! $narrowing->assertedType instanceof Type) {
                continue;
            }

            $specifiedTypes = $specifiedTypes->unionWith($this->typeSpecifier->create(
                $narrowing->subject,
                $narrowing->assertedType,
                $context,
                $scope,
            ));
        }

        return $specifiedTypes;
    }
}
