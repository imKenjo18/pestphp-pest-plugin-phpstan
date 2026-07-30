<?php

declare(strict_types=1);

namespace Pest\PHPStan\Type\Pest;

use Pest\Arch\Contracts\ArchExpectation;
use PhpParser\Node;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Error;
use PHPStan\Analyser\IgnoreErrorExtension;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;

final class ArchExpectationPropertyIgnoreExtension implements IgnoreErrorExtension
{
    public function shouldIgnore(Error $error, Node $node, Scope $scope): bool
    {
        if ($error->getIdentifier() !== 'property.notFound') {
            return false;
        }

        if (! $node instanceof PropertyFetch) {
            return false;
        }

        if (! $node->name instanceof Identifier || ! in_array($node->name->name, ExpectationPropertiesExtension::KNOWN_EXPECTATION_PROPERTIES, true)) {
            return false;
        }

        return new ObjectType(ArchExpectation::class)
            ->isSuperTypeOf($scope->getType($node->var))
            ->yes();
    }
}
