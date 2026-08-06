<?php

declare(strict_types=1);

namespace Pest\PHPStan\Analysis\Expectation;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;

final class ExpectationChainSubjectResolver
{
    public function subjectIntroducedBy(Expr $receiver): ?Expr
    {
        if ($receiver instanceof MethodCall) {
            if (! $receiver->name instanceof Identifier || $receiver->name->toString() !== 'and') {
                return null;
            }

            $args = $receiver->getArgs();

            return $args === [] ? null : $args[0]->value;
        }

        if ($receiver instanceof FuncCall) {
            if (! $receiver->name instanceof Name || $receiver->name->toString() !== 'expect') {
                return null;
            }

            $args = $receiver->getArgs();

            return $args === [] ? null : $args[0]->value;
        }

        return null;
    }
}
