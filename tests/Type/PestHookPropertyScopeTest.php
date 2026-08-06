<?php

declare(strict_types=1);

namespace Tests\Type;

use Pest\PHPStan\Type\Pest\PestFileDiscoverer;
use Pest\PHPStan\Type\Pest\PestHookPropertyReader;
use RuntimeException;

$hooks = static function (): array {
    $fixtureDir = realpath(__DIR__.'/Fixtures/pesthook-scope');

    if ($fixtureDir === false) {
        throw new RuntimeException('Hook scope fixture directory not found.');
    }

    return [$fixtureDir, new PestHookPropertyReader(new PestFileDiscoverer([$fixtureDir]))];
};

test('a beforeEach scoped with in() applies to files under that target', function () use ($hooks): void {
    [$dir, $reader] = $hooks();

    expect($reader->getPropertyExprs($dir.'/Scoped/ScopedTest.php'))->toHaveKey('scopedProperty');
});

test('a beforeEach scoped with in() does not leak into a sibling directory', function () use ($hooks): void {
    [$dir, $reader] = $hooks();

    expect($reader->getPropertyExprs($dir.'/Sibling/SiblingTest.php'))->not->toHaveKey('scopedProperty');
});

test('an arrow function beforeEach scoped with in() applies to files under that target', function () use ($hooks): void {
    [$dir, $reader] = $hooks();

    expect($reader->getPropertyExprs($dir.'/ArrowScoped/ArrowScopedTest.php'))->toHaveKey('arrowScopedProperty');
});

test('an arrow function beforeEach scoped with in() does not leak into a sibling directory', function () use ($hooks): void {
    [$dir, $reader] = $hooks();

    expect($reader->getPropertyExprs($dir.'/Sibling/SiblingTest.php'))->not->toHaveKey('arrowScopedProperty');
});

test('a beforeEach without an in() target binds to no file at all', function () use ($hooks): void {
    [$dir, $reader] = $hooks();

    expect($reader->getPropertyExprs($dir.'/Scoped/ScopedTest.php'))->not->toHaveKey('untargetedProperty')
        ->and($reader->getPropertyExprs($dir.'/Sibling/SiblingTest.php'))->not->toHaveKey('untargetedProperty')
        ->and($reader->getPropertyExprs($dir.'/Pest.php'))->not->toHaveKey('untargetedProperty');
});
