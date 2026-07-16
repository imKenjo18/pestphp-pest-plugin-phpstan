<?php

declare(strict_types=1);

namespace Tests\Rules;

use PHPStan\Rules\Methods\CallMethodsRule;
use Tests\RuleTestCase;

beforeAll(function (): void {
    RuleTestCase::$additionalConfigFiles = [
        __DIR__.'/../Type/custom-testcase-extension.neon',
    ];
    RuleTestCase::$rule = RuleTestCase::resolveRule(CallMethodsRule::class);
});

test('protected method calls are allowed with custom test case', function (): void {
    $this->analyse([
        __DIR__.'/../Fixtures/CustomTestCaseInference/Feature/protected-method-calls-custom-testcase.php',
    ], []);
});
