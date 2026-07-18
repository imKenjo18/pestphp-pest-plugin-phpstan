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

test('duplicate descriptions are reported across chains and it prefixes', function (): void {
    $this->analyse([
        __DIR__.'/data/empty-and-duplicate-exhaustive.php',
    ], [
        [
            "A test with the description 'it duplicated it' already exists in this file.",
            19,
        ],
        [
            "A test with the description 'duplicated test' already exists in this file.",
            27,
        ],
        [
            "A test with the description 'it collides with a test' already exists in this file.",
            35,
        ],
        [
            "A test with the description 'it triplicated' already exists in this file.",
            43,
        ],
        [
            "A test with the description 'it triplicated' already exists in this file.",
            47,
        ],
        [
            "A test with the description 'it duplicated through a chain' already exists in this file.",
            55,
        ],
    ]);
});
