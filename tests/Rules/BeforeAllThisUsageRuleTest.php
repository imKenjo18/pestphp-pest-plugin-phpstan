<?php

declare(strict_types=1);

namespace Tests\Rules;

use Pest\PHPStan\Rules\BeforeAllThisUsageRule;
use Tests\RuleTestCase;

beforeAll(function (): void {
    RuleTestCase::$rule = new BeforeAllThisUsageRule;
    RuleTestCase::$additionalConfigFiles = [
        __DIR__.'/../extension.neon',
    ];
});

test('this usage in before all is reported', function (): void {
    $this->analyse([
        __DIR__.'/data/before-all-this-usage.php',
    ], [
        [
            'beforeAll() runs in static context — $this is not available. Use beforeEach() instead.',
            6,
        ],
        [
            'beforeAll() runs in static context — $this is not available. Use beforeEach() instead.',
            10,
        ],
    ]);
});

test('this usage in after all is reported', function (): void {
    $this->analyse([
        __DIR__.'/data/after-all-this-usage.php',
    ], [
        [
            'afterAll() runs in static context — $this is not available. Use afterEach() instead.',
            6,
        ],
        [
            'afterAll() runs in static context — $this is not available. Use afterEach() instead.',
            10,
        ],
    ]);
});
