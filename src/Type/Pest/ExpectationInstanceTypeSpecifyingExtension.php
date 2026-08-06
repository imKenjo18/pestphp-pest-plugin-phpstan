<?php

declare(strict_types=1);

namespace Pest\PHPStan\Type\Pest;

use Pest\Expectation;
use Pest\PHPStan\Analysis\Expectation\ExpectationChainSubjectResolver;
use Pest\PHPStan\Analysis\Expectation\ExpectationMatcherRegistry;
use Pest\PHPStan\Analysis\Expectation\ExpectationTypeNarrower;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Analyser\SpecifiedTypes;
use PHPStan\Analyser\TypeSpecifier;
use PHPStan\Analyser\TypeSpecifierAwareExtension;
use PHPStan\Analyser\TypeSpecifierContext;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\MethodTypeSpecifyingExtension;
use PHPStan\Type\Type;

final class ExpectationInstanceTypeSpecifyingExtension implements MethodTypeSpecifyingExtension, TypeSpecifierAwareExtension
{
    private TypeSpecifier $typeSpecifier;

    public function __construct(
        private readonly ExpectationMatcherRegistry $matcherRegistry,
        private readonly ExpectationTypeNarrower $typeNarrower,
        private readonly ExpectationChainSubjectResolver $subjectResolver,
    ) {}

    public function setTypeSpecifier(TypeSpecifier $typeSpecifier): void
    {
        $this->typeSpecifier = $typeSpecifier;
    }

    public function getClass(): string
    {
        return Expectation::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection, MethodCall $node, TypeSpecifierContext $context): bool
    {
        if (! $context->null()) {
            return false;
        }

        return $this->collectFacts($node) !== [];
    }

    public function specifyTypes(MethodReflection $methodReflection, MethodCall $node, Scope $scope, TypeSpecifierContext $context): SpecifiedTypes
    {
        $result = new SpecifiedTypes([], []);

        foreach ($this->collectFacts($node) as [$subjectExpr, $toBeInstanceOfCall]) {
            $assertedType = $this->matcherRegistry->assertedTypeFor('toBeInstanceOf', $toBeInstanceOfCall, $scope);
            if (! $assertedType instanceof Type) {
                continue;
            }

            $incomingType = $scope->getType($subjectExpr);
            if (! $this->typeNarrower->hasOverlap($incomingType, $assertedType)) {
                continue;
            }

            $narrowedType = $this->typeNarrower->narrow($incomingType, $assertedType);

            $result = $result->unionWith(
                $this->typeSpecifier->create($subjectExpr, $narrowedType, TypeSpecifierContext::createTrue(), $scope)
            );
        }

        return $result;
    }

    /**
     * @return list<array{0: Expr, 1: MethodCall}>
     */
    private function collectFacts(MethodCall $node): array
    {
        $facts = [];
        $current = $node;

        while ($current instanceof MethodCall) {
            if ($current->name instanceof Identifier && $current->name->toString() === 'toBeInstanceOf') {
                $subjectExpr = $this->subjectResolver->subjectIntroducedBy($current->var);
                if ($subjectExpr instanceof Expr) {
                    $facts[] = [$subjectExpr, $current];
                }
            }

            $current = $current->var;
        }

        return $facts;
    }
}
