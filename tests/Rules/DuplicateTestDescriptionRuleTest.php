<?php

declare(strict_types=1);

namespace Tests\Rules;

use Pest\PHPStan\Rules\DuplicateTestDescriptionRule;
use Tests\RuleTestCase;

beforeAll(function (): void {
    RuleTestCase::$rule = new DuplicateTestDescriptionRule;
    RuleTestCase::$additionalConfigFiles = [
        __DIR__.'/../extension.neon',
    ];
});

test('duplicate descriptions are reported', function (): void {
    $this->analyse([
        __DIR__.'/data/duplicate-test-description.php',
    ], [
        [
            "A test with the description 'it does something' already exists in this file.",
            9,
        ],
        [
            "A test with the description 'another test' already exists in this file.",
            17,
        ],
        [
            "A test with the description 'it matches cross-function' already exists in this file.",
            25,
        ],
    ]);
});
