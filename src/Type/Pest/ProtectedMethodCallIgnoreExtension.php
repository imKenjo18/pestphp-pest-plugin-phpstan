<?php

declare(strict_types=1);

namespace Pest\PHPStan\Type\Pest;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Error;
use PHPStan\Analyser\IgnoreErrorExtension;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use PHPUnit\Framework\TestCase;

final class ProtectedMethodCallIgnoreExtension implements IgnoreErrorExtension
{
    public function shouldIgnore(Error $error, Node $node, Scope $scope): bool
    {
        if ($error->getIdentifier() !== 'method.protected') {
            return false;
        }

        if (! $scope->isInAnonymousFunction()) {
            return false;
        }

        if (! $node instanceof MethodCall) {
            return false;
        }

        if (! $node->var instanceof Variable || $node->var->name !== 'this') {
            return false;
        }

        if (! $scope->hasVariableType('this')->yes()) {
            return false;
        }

        $thisType = $scope->getVariableType('this');

        return (new ObjectType(TestCase::class))
            ->isSuperTypeOf($thisType)
            ->yes();
    }
}
