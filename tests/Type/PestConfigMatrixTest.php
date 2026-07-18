<?php

declare(strict_types=1);

namespace Tests\Type;

use Pest\PHPStan\Type\Pest\PestConfigReader;
use Pest\PHPStan\Type\Pest\PestFileDiscoverer;
use RuntimeException;
use Tests\Type\Fixtures\AnotherTestCase;
use Tests\Type\Fixtures\CustomTestCase;
use Tests\Type\Fixtures\HelperTrait;

$matrix = static function (): array {
    $fixtureDir = realpath(__DIR__.'/Fixtures/pestconfig-matrix');

    if ($fixtureDir === false) {
        throw new RuntimeException('Matrix fixture directory not found.');
    }

    return [$fixtureDir, new PestConfigReader(new PestFileDiscoverer([$fixtureDir]))];
};

test('a directory target binds files directly inside it', function () use ($matrix): void {
    [$dir, $reader] = $matrix();

    expect($reader->resolveBindings($dir.'/Feature/SomeTest.php'))->toContain(CustomTestCase::class);
});

test('a directory target binds files in nested subdirectories', function () use ($matrix): void {
    [$dir, $reader] = $matrix();

    expect($reader->resolveBindings($dir.'/Feature/Nested/DeepTest.php'))->toContain(CustomTestCase::class);
});

test('each target of a multi-target in() is bound', function () use ($matrix): void {
    [$dir, $reader] = $matrix();

    expect($reader->resolveBindings($dir.'/Unit/UnitTest.php'))->toContain(AnotherTestCase::class)
        ->and($reader->resolveBindings($dir.'/Browser/BrowserTest.php'))->toContain(AnotherTestCase::class);
});

test('a single-file target binds only that file', function () use ($matrix): void {
    [$dir, $reader] = $matrix();

    expect($reader->resolveBindings($dir.'/Single/OnlyThis.php'))->toContain(HelperTrait::class)
        ->and($reader->resolveBindings($dir.'/Single/NotThis.php'))->not->toContain(HelperTrait::class);
});

test('a directory glob binds every matching directory', function () use ($matrix): void {
    [$dir, $reader] = $matrix();

    expect($reader->resolveBindings($dir.'/Api/V1/V1Test.php'))->toContain(CustomTestCase::class)
        ->and($reader->resolveBindings($dir.'/Api/V2/V2Test.php'))->toContain(CustomTestCase::class);
});

test('a file glob binds only the matching files', function () use ($matrix): void {
    [$dir, $reader] = $matrix();

    expect($reader->resolveBindings($dir.'/Globbed/AlphaTest.php'))->toContain(HelperTrait::class)
        ->and($reader->resolveBindings($dir.'/Globbed/BetaTest.php'))->toContain(HelperTrait::class)
        ->and($reader->resolveBindings($dir.'/Globbed/helper.php'))->not->toContain(HelperTrait::class);
});

test('a path covered by no target receives no fixture bindings', function () use ($matrix): void {
    [$dir, $reader] = $matrix();

    expect($reader->resolveBindings($dir.'/Unmatched/StrayTest.php'))
        ->not->toContain(CustomTestCase::class)
        ->not->toContain(AnotherTestCase::class)
        ->not->toContain(HelperTrait::class);
});

test('a directory target does not leak into a sibling directory', function () use ($matrix): void {
    [$dir, $reader] = $matrix();

    expect($reader->resolveBindings($dir.'/Unit/UnitTest.php'))->not->toContain(CustomTestCase::class);
});

test('the union of bound classes covers every declaration', function () use ($matrix): void {
    [, $reader] = $matrix();

    expect($reader->allBoundClasses())
        ->toContain(CustomTestCase::class)
        ->toContain(AnotherTestCase::class)
        ->toContain(HelperTrait::class);
});

test('a path outside the fixture tree resolves to no bindings', function () use ($matrix): void {
    [, $reader] = $matrix();

    expect($reader->resolveBindings('/definitely/not/here/Test.php'))->toBeEmpty();
});

test('the pest configuration file itself is never bound to its own targets', function () use ($matrix): void {
    [$dir, $reader] = $matrix();

    expect($reader->resolveBindings($dir.'/Pest.php'))
        ->not->toContain(CustomTestCase::class)
        ->not->toContain(AnotherTestCase::class);
});
