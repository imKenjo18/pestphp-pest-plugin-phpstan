<?php

declare(strict_types=1);

namespace Pest\PHPStan\Type\Pest;

use LogicException;
use Pest\Concerns\Testable;
use Pest\PendingCalls\TestCall;
use Pest\Support\HigherOrderCallables;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Reflection\ReflectionProvider;

final class TestCallMethodsClassReflectionExtension implements MethodsClassReflectionExtension
{
    /** @var list<class-string> */
    private const MIXIN_CLASSES = [
        Testable::class,
        HigherOrderCallables::class,
    ];

    public function __construct(
        private readonly ReflectionProvider $reflectionProvider,
        private readonly PestConfigReader $pestConfigReader,
    ) {}

    public function hasMethod(ClassReflection $classReflection, string $methodName): bool
    {
        if (! $classReflection->is(TestCall::class)) {
            return false;
        }

        if ($classReflection->hasNativeMethod($methodName)) {
            return false;
        }

        return array_any($this->mixinClasses(), fn (string $mixinClass): bool => $this->hasPublicMixinMethod($mixinClass, $methodName));
    }

    public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        foreach ($this->mixinClasses() as $mixinClass) {
            if ($this->hasPublicMixinMethod($mixinClass, $methodName)) {
                return $this->reflectionProvider->getClass($mixinClass)->getNativeMethod($methodName);
            }
        }

        throw new LogicException(sprintf('Method %s not found on any TestCall mixin class.', $methodName));
    }

    /** @return list<string> */
    private function mixinClasses(): array
    {
        return array_values(array_unique([
            ...self::MIXIN_CLASSES,
            ...$this->pestConfigReader->allBoundClasses(),
        ]));
    }

    private function hasPublicMixinMethod(string $mixinClass, string $methodName): bool
    {
        if (! $this->reflectionProvider->hasClass($mixinClass)) {
            return false;
        }

        $mixinReflection = $this->reflectionProvider->getClass($mixinClass);
        if (! $mixinReflection->hasNativeMethod($methodName)) {
            return false;
        }

        return $mixinReflection->getNativeMethod($methodName)->isPublic();
    }
}
