<?php

declare(strict_types=1);

namespace Tests\Rules;

use PHPStan\Rules\Properties\AccessPropertiesRule;
use Tests\RuleTestCase;

beforeAll(function (): void {
    RuleTestCase::$additionalConfigFiles = [
        __DIR__.'/../extension.neon',
    ];
    RuleTestCase::$rule = RuleTestCase::resolveRule(AccessPropertiesRule::class);
});

test('not property access on arch expectations does not report undefined property', function (): void {
    $this->analyse([
        __DIR__.'/data/arch-expectation-not-property.php',
    ], []);
});
