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

    private const string NEGATE_METHOD = 'not';

    private const array PASSTHROUGH_METHODS = ['when', 'unless', 'sequence', 'match', 'ray'];

    /** @var array<string, bool> Matcher name => uses loose (==) comparison */
    private const array COMPARISON_METHODS = ['toBe' => false, 'toEqual' => true];

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
        $negated = false;

        foreach (array_reverse($links) as $link) {
            if ($link instanceof PropertyFetch) {
                /** @var Identifier $name */
                $name = $link->name;
                if ($name->name === self::NEGATE_METHOD) {
                    $negated = ! $negated;

                    continue;
                }

                return $narrowings;
            }

            /** @var Identifier $name */
            $name = $link->name;
            $methodName = $name->name;

            if ($methodName === self::NEGATE_METHOD && $link->getArgs() === []) {
                $negated = ! $negated;

                continue;
            }

            if ($methodName === self::REBIND_METHOD) {
                $subject = $link->getArgs()[0]->value ?? null;
                if (! $subject instanceof Expr) {
                    return $narrowings;
                }

                $negated = false;

                continue;
            }

            if (in_array($methodName, self::PASSTHROUGH_METHODS, true)) {
                continue;
            }

            if (! method_exists(MixinsExpectation::class, $methodName)) {
                return $narrowings;
            }

            if (isset(self::COMPARISON_METHODS[$methodName])) {
                $compared = $link->getArgs()[0]->value ?? null;
                if ($compared instanceof Expr) {
                    $narrowings[] = ExpectationNarrowing::comparison($subject, $compared, self::COMPARISON_METHODS[$methodName], $negated);
                }

                $negated = false;

                continue;
            }

            $assertedType = $this->matcherRegistry->assertedTypeFor($methodName, $link, $scope);
            if ($assertedType instanceof Type) {
                $narrowings[] = ExpectationNarrowing::type($subject, $assertedType, $negated);
            }

            $negated = false;
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
