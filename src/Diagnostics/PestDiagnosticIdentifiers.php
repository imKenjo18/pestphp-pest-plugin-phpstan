<?php

declare(strict_types=1);

namespace Pest\PHPStan\Diagnostics;

final class PestDiagnosticIdentifiers
{
    public const string TEST_DUPLICATE_DESCRIPTION = 'pest.test.duplicateDescription';

    public const string GROUP_INVALID_NAME = 'pest.group.invalidName';

    public const string TEST_STATIC_CLOSURE = 'pest.test.staticClosure';

    public const string REPEAT_INVALID_VALUE = 'pest.execution.invalidRepeatValue';

    public const string DESCRIBE_BEFORE_ALL_DISALLOWED = 'pest.lifecycle.beforeAllDisallowed';

    public const string DESCRIBE_AFTER_ALL_DISALLOWED = 'pest.lifecycle.afterAllDisallowed';

    public const string COVERS_FUNCTION_NOT_FOUND = 'pest.covers.functionNotFound';

    public const string COVERS_CLASS_NOT_FOUND = 'pest.covers.classNotFound';

    public const string TEST_EMPTY_CLOSURE = 'pest.test.emptyClosure';

    public const string THROWS_CLASS_NOT_FOUND = 'pest.throws.classNotFound';

    public const string THROWS_INVALID_EXCEPTION = 'pest.throws.invalidException';

    public const string DESCRIBE_WITHOUT_TESTS = 'pest.describe.withoutTests';

    public const string EXPECTATION_REQUIRES_STRING = 'pest.expectation.requiresString';

    public const string EXPECTATION_REQUIRES_ITERABLE = 'pest.expectation.requiresIterable';

    public const string EXPECTATION_REQUIRES_COUNTABLE_OR_ITERABLE = 'pest.expectation.requiresCountableOrIterable';

    public const string EXPECTATION_IMPOSSIBLE = 'pest.expectation.impossible';

    public const string EXPECTATION_REDUNDANT = 'pest.expectation.redundant';

    public const string LIFECYCLE_BEFORE_ALL_THIS_USAGE = 'pest.lifecycle.beforeAllThisUsage';

    public const string LIFECYCLE_AFTER_ALL_THIS_USAGE = 'pest.lifecycle.afterAllThisUsage';

    public const string CONFIG_REDUNDANT_LOCAL_USE = 'pest.config.redundantLocalUse';
}
