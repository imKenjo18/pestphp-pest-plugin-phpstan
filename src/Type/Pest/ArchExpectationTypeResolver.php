<?php

declare(strict_types=1);

namespace Pest\PHPStan\Type\Pest;

use Pest\Expectation;
use Pest\Expectations\OppositeExpectation;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;

final class ArchExpectationTypeResolver
{
    public static function getNotPropertyType(ClassReflection $classReflection): Type
    {
        foreach ($classReflection->getResolvedMixinTypes() as $mixinType) {
            $mixinClassReflections = $mixinType->getObjectClassReflections();
            foreach ($mixinClassReflections as $mixinClassReflection) {
                if ($mixinClassReflection->is(Expectation::class)) {
                    $tValue = $mixinType->getTemplateType(Expectation::class, 'TValue');

                    return new GenericObjectType(OppositeExpectation::class, [$tValue]);
                }
            }
        }

        return new GenericObjectType(OppositeExpectation::class, [new ObjectType('string')]);
    }
}
