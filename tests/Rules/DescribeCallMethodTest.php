<?php

declare(strict_types=1);

namespace Tests\Rules;

use PHPStan\Rules\Methods\CallMethodsRule;
use Tests\RuleTestCase;

beforeAll(function (): void {
    RuleTestCase::$additionalConfigFiles = [
        __DIR__.'/../extension.neon',
    ];
    RuleTestCase::$rule = RuleTestCase::resolveRule(CallMethodsRule::class);
});

test('describe call chain methods are allowed', function (): void {
    $this->analyse([
        __DIR__.'/data/describe-call-methods.php',
    ], []);
});
