<?php

declare(strict_types=1);

namespace Pest\PHPStan\Analysis\Expectation;

use Pest\Expectation;
use Pest\Mixins\Expectation as MixinsExpectation;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;

final class ExpectationNarrowingResolver
{
    private const string REBIND_METHOD = 'and';

    private const array PASSTHROUGH_METHODS = ['when', 'unless', 'sequence', 'match', 'ray'];

    public function __construct(
        private readonly ExpectationMatcherRegistry $matcherRegistry,
    ) {}

    /**
     * @return list<ExpectationNarrowing>
     */
    public function resolve(MethodCall $methodCall, Scope $scope): array
    {
        $links = [];
        $current = $methodCall;

        while (($current instanceof MethodCall || $current instanceof PropertyFetch) && $current->name instanceof Identifier) {
            $links[] = $current;
            $current = $current->var;
        }

        $subject = $this->resolveSubject($current, $scope);
        if (! $subject instanceof Expr) {
            return [];
        }

        $narrowings = [];

        foreach (array_reverse($links) as $link) {
            if ($link instanceof PropertyFetch) {
                return $narrowings;
            }

            /** @var Identifier $name */
            $name = $link->name;
            $methodName = $name->name;

            if ($methodName === self::REBIND_METHOD) {
                $subject = $link->getArgs()[0]->value ?? null;
                if (! $subject instanceof Expr) {
                    return $narrowings;
                }

                continue;
            }

            if (in_array($methodName, self::PASSTHROUGH_METHODS, true)) {
                continue;
            }

            if (! method_exists(MixinsExpectation::class, $methodName)) {
                return $narrowings;
            }

            $assertedType = $this->matcherRegistry->assertedTypeFor($methodName, $link, $scope);
            if ($assertedType instanceof Type) {
                $narrowings[] = ExpectationNarrowing::type($subject, $assertedType);
            }
        }

        return $narrowings;
    }

    private function resolveSubject(Expr $root, Scope $scope): ?Expr
    {
        if (! $root instanceof FuncCall || ! $root->name instanceof Name) {
            return null;
        }

        if ($root->name->toLowerString() !== 'expect') {
            return null;
        }

        if (! new ObjectType(Expectation::class)->isSuperTypeOf($scope->getType($root))->yes()) {
            return null;
        }

        return $root->getArgs()[0]->value ?? null;
    }
}
