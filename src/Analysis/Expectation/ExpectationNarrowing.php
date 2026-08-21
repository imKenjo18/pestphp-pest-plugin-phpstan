<?php

declare(strict_types=1);

namespace Pest\PHPStan\Analysis\Expectation;

use PhpParser\Node\Expr;
use PHPStan\Type\Type;

final readonly class ExpectationNarrowing
{
    private function __construct(
        public Expr $subject,
        public ?Type $assertedType,
        public ?Expr $comparedExpr,
        public bool $loose,
        public bool $negated,
    ) {}

    public static function type(Expr $subject, Type $assertedType, bool $negated = false): self
    {
        return new self($subject, $assertedType, null, false, $negated);
    }

    public static function comparison(Expr $subject, Expr $comparedExpr, bool $loose, bool $negated): self
    {
        return new self($subject, null, $comparedExpr, $loose, $negated);
    }
}
