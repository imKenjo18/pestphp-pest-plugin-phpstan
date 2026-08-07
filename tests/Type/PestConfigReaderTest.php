<?php

declare(strict_types=1);

namespace Tests\Type;

use Pest\PHPStan\Type\Pest\PestConfigReader;
use Pest\PHPStan\Type\Pest\PestFileDiscoverer;
use RuntimeException;
use Tests\Rules\Fixtures\DynamicTrait;
use Tests\Rules\Fixtures\MixedDynamicTrait;
use Tests\Rules\Fixtures\OtherTrait;
use Tests\Rules\Fixtures\RefreshDatabase;
use Tests\Type\Fixtures\CustomTestCase;
use Tests\Type\Fixtures\HelperTrait;

$makeReader = static function (): array {
    $fixtureDir = realpath(__DIR__.'/Fixtures/pestconfig');

    if ($fixtureDir === false) {
        throw new RuntimeException('Fixture directory not found.');
    }

    $discoverer = new PestFileDiscoverer([$fixtureDir]);

    return [$fixtureDir, new PestConfigReader($discoverer)];
};

test('resolves extend binding for feature directory', function () use ($makeReader): void {
    [$fixtureDir, $reader] = $makeReader();

    $bindings = $reader->resolveBindings($fixtureDir.'/Feature/SomeTest.php');

    expect($bindings)->toContain(CustomTestCase::class);
});

test('resolves extend binding for unit directory', function () use ($makeReader): void {
    [$fixtureDir, $reader] = $makeReader();

    $bindings = $reader->resolveBindings($fixtureDir.'/Unit/SomeTest.php');

    expect($bindings)->toContain(CustomTestCase::class);
});

test('resolves use binding for helpers subdirectory', function () use ($makeReader): void {
    [$fixtureDir, $reader] = $makeReader();

    $bindings = $reader->resolveBindings($fixtureDir.'/Feature/Helpers/SomeTest.php');

    expect($bindings)->toContain(HelperTrait::class);
});

test('accumulates parent and subdirectory bindings', function () use ($makeReader): void {
    [$fixtureDir, $reader] = $makeReader();

    $bindings = $reader->resolveBindings($fixtureDir.'/Feature/Helpers/SomeTest.php');

    expect($bindings)
        ->toContain(CustomTestCase::class)
        ->toContain(HelperTrait::class);
});

test('collects the union of all bound classes across directories', function () use ($makeReader): void {
    [, $reader] = $makeReader();

    $bindings = $reader->allBoundClasses();

    expect($bindings)
        ->toContain(CustomTestCase::class)
        ->toContain(HelperTrait::class);
});

test('returns empty for unmatched path', function () use ($makeReader): void {
    [, $reader] = $makeReader();

    $bindings = $reader->resolveBindings('/some/other/path/Test.php');

    expect($bindings)->toBeEmpty();
});

test('resolves global bindings without in() scope to every file', function (): void {
    $fixtureDir = realpath(__DIR__.'/Fixtures/pestconfig-global');
    if ($fixtureDir === false) {
        throw new RuntimeException('Global fixtures directory not found.');
    }

    $reader = new PestConfigReader(new PestFileDiscoverer([$fixtureDir]));

    expect($reader->resolveBindings($fixtureDir.'/Feature/SomeTest.php'))
        ->toContain(CustomTestCase::class)
        ->toContain(HelperTrait::class)
        ->and($reader->resolveBindings('/some/other/path/Test.php'))
        ->toContain(CustomTestCase::class)
        ->toContain(HelperTrait::class);
});

test('resolves in() directories built from __DIR__', function (): void {
    $fixtureDir = realpath(__DIR__.'/Fixtures/pestconfig-magicdir');
    if ($fixtureDir === false) {
        throw new RuntimeException('Magic dir fixture directory not found.');
    }

    $reader = new PestConfigReader(new PestFileDiscoverer([$fixtureDir]));

    expect($reader->resolveBindings($fixtureDir.'/Feature/SomeTest.php'))
        ->toContain(CustomTestCase::class);
});

test('expands glob patterns in in() targets like pest does', function (): void {
    $fixtureDir = realpath(__DIR__.'/Fixtures/pestconfig-glob');
    if ($fixtureDir === false) {
        throw new RuntimeException('Glob fixture directory not found.');
    }

    $reader = new PestConfigReader(new PestFileDiscoverer([$fixtureDir]));

    expect($reader->resolveBindings($fixtureDir.'/Feature/OtherTest.php'))
        ->toContain(CustomTestCase::class);
});

test('resolves single-file in() targets to that file only', function (): void {
    $fixtureDir = realpath(__DIR__.'/Fixtures/pestconfig-glob');
    if ($fixtureDir === false) {
        throw new RuntimeException('Glob fixture directory not found.');
    }

    $reader = new PestConfigReader(new PestFileDiscoverer([$fixtureDir]));

    expect($reader->resolveBindings($fixtureDir.'/Feature/ExampleTest.php'))
        ->toContain(HelperTrait::class)
        ->and($reader->resolveBindings($fixtureDir.'/Feature/OtherTest.php'))
        ->not->toContain(HelperTrait::class);
});

test('ignores pest files with syntax errors instead of crashing', function (): void {
    $temporaryDir = sys_get_temp_dir().'/pest-phpstan-syntax-error-'.uniqid();
    mkdir($temporaryDir.'/Feature', 0755, true);
    file_put_contents($temporaryDir.'/Pest.php', "<?php\nuses(Foo::class->in('Feature');\n");

    try {
        $reader = new PestConfigReader(new PestFileDiscoverer([$temporaryDir]));

        expect($reader->resolveBindings($temporaryDir.'/Feature/SomeTest.php'))->toBeArray()
            ->and($reader->resolveFileBindings($temporaryDir.'/Pest.php'))->toBeArray();
    } finally {
        unlink($temporaryDir.'/Pest.php');
        rmdir($temporaryDir.'/Feature');
        rmdir($temporaryDir);
    }
});

test('resolves statically known global uses and skips dynamic paths', function (): void {
    $fixtureDir = realpath(__DIR__.'/../Rules/data/redundant-local-use');
    if ($fixtureDir === false) {
        throw new RuntimeException('Redundant local use fixture directory not found.');
    }

    $reader = new PestConfigReader(new PestFileDiscoverer([$fixtureDir]));
    $bindings = $reader->resolveGlobalUses($fixtureDir.'/Feature/uses.php');

    expect(array_column($bindings, 'class'))
        ->toContain(RefreshDatabase::class)
        ->not->toContain(CustomTestCase::class)
        ->not->toContain(DynamicTrait::class)
        ->toContain(MixedDynamicTrait::class)
        ->and($reader->resolveBindings($fixtureDir.'/Feature/uses.php'))->toContain(CustomTestCase::class)
        ->toContain(RefreshDatabase::class)
        ->and(array_column($reader->resolveGlobalUses($fixtureDir.'/PluralFeature/multiple.php'), 'class'))
        ->toContain(OtherTrait::class)
        ->not->toContain(CustomTestCase::class)
        ->and(array_column($reader->resolveGlobalUses($fixtureDir.'/StandaloneFeature/uses.php'), 'class'))
        ->toContain(DynamicTrait::class);
});
