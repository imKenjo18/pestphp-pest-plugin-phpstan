<?php

declare(strict_types=1);

namespace Tests\Rules;

use Pest\PHPStan\Rules\CoversClassExistsRule;
use Tests\RuleTestCase;

beforeAll(function (): void {
    RuleTestCase::$additionalConfigFiles = [
        __DIR__.'/../extension.neon',
    ];
    RuleTestCase::$rule = RuleTestCase::resolveRule(CoversClassExistsRule::class);
});

test('non existent class in covers class is reported', function (): void {
    $this->analyse([
        __DIR__.'/data/covers-class-exists.php',
    ], [
        [
            'Class App\NonExistent\FakeClass referenced in coversClass() does not exist.',
            7,
        ],
        [
            'Class App\NonExistent\SecondFakeClass referenced in coversClass() does not exist.',
            14,
        ],
        [
            'Function missing_test_helper() referenced in coversFunction() does not exist.',
            17,
        ],
    ]);
});
