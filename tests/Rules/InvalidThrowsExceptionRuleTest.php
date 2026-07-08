<?php

declare(strict_types=1);

namespace Tests\Rules;

use Pest\PHPStan\Rules\InvalidThrowsExceptionRule;
use Tests\RuleTestCase;

beforeAll(function (): void {
    RuleTestCase::$additionalConfigFiles = [
        __DIR__.'/../extension.neon',
    ];
    RuleTestCase::$rule = RuleTestCase::resolveRule(InvalidThrowsExceptionRule::class);
});

test('non throwable class in throws is reported', function (): void {
    $this->analyse([
        __DIR__.'/data/invalid-throws-exception.php',
    ], [
        [
            'throws() expects a Throwable class, got stdClass.',
            5,
        ],
    ]);
});
