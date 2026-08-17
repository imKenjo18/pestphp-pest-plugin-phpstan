<?php

declare(strict_types=1);

namespace Pest\PHPStan\Type\Pest;

use Pest\PHPStan\Analysis\Expectation\ExpectationNarrowingResolver;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Analyser\SpecifiedTypes;
use PHPStan\Analyser\TypeSpecifier;
use PHPStan\Analyser\TypeSpecifierAwareExtension;
use PHPStan\Analyser\TypeSpecifierContext;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\MethodTypeSpecifyingExtension;

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
            $specifiedTypes = $specifiedTypes->unionWith($this->typeSpecifier->create(
                $narrowing->subject,
                $narrowing->assertedType,
                $narrowing->negated
                    ? TypeSpecifierContext::createTruthy()->negate()
                    : TypeSpecifierContext::createTruthy(),
                $scope,
            ));
        }

        return $specifiedTypes;
    }
}
