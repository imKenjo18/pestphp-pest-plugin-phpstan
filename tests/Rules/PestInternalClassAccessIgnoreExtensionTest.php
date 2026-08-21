<?php

declare(strict_types=1);

namespace Tests\Rules;

use PHPStan\Rules\RestrictedUsage\RestrictedMethodUsageRule;
use Tests\RuleTestCase;

beforeAll(function (): void {
    RuleTestCase::$additionalConfigFiles = [
        __DIR__.'/internal-tag.neon',
    ];
    RuleTestCase::$rule = RuleTestCase::resolveRule(RestrictedMethodUsageRule::class);
});

test('methods of internal pest interfaces are not reported on arch expectations', function (): void {
    $this->analyse([
        __DIR__.'/data/arch-expectation-internal-methods.php',
    ], []);
});
