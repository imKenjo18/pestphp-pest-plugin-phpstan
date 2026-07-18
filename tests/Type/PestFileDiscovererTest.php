<?php

declare(strict_types=1);

namespace Tests\Type;

use Pest\PHPStan\Type\Pest\PestFileDiscoverer;
use RuntimeException;

$fixtureDir = static function (string $name): string {
    $path = realpath(__DIR__.'/Fixtures/'.$name);

    if ($path === false) {
        throw new RuntimeException(sprintf('Fixture directory %s not found.', $name));
    }

    return $path;
};

$discoverer = static fn (string $dir): PestFileDiscoverer => new PestFileDiscoverer([$dir]);

test('a discovered Pest.php is recognised as a configuration file', function () use ($fixtureDir, $discoverer): void {
    $dir = $fixtureDir('pestconfig-matrix');

    expect($discoverer($dir)->isPestConfigFile($dir.'/Pest.php'))->toBeTrue();
});

test('an ordinary test file is not a configuration file', function () use ($fixtureDir, $discoverer): void {
    $dir = $fixtureDir('pestconfig-matrix');

    expect($discoverer($dir)->isPestConfigFile($dir.'/Feature/SomeTest.php'))->toBeFalse()
        ->and($discoverer($dir)->isPestConfigFile($dir.'/Unit/UnitTest.php'))->toBeFalse();
});

test('a non-existent path is not a configuration file', function () use ($fixtureDir, $discoverer): void {
    $dir = $fixtureDir('pestconfig-matrix');

    expect($discoverer($dir)->isPestConfigFile($dir.'/Nope/Missing.php'))->toBeFalse()
        ->and($discoverer($dir)->isPestConfigFile('/definitely/not/here/Pest.php'))->toBeFalse();
});

test('a file merely named Pest.php outside the scanned tree is not recognised', function () use ($fixtureDir, $discoverer): void {
    $scoped = $discoverer($fixtureDir('pesthook-scope'));

    expect($scoped->isPestConfigFile($fixtureDir('pestconfig-matrix').'/Pest.php'))->toBeFalse()
        ->and($scoped->isPestConfigFile($fixtureDir('pesthook-scope').'/Pest.php'))->toBeTrue();
});

test('config file recognition is independent of path formatting', function () use ($fixtureDir, $discoverer): void {
    $dir = $fixtureDir('pestconfig-matrix');

    expect($discoverer($dir)->isPestConfigFile($dir.'/Feature/../Pest.php'))->toBeTrue()
        ->and($discoverer($dir)->isPestConfigFile($dir.'/./Pest.php'))->toBeTrue();
});

test('discovery is memoized and returns a stable list across calls', function () use ($fixtureDir, $discoverer): void {
    $instance = $discoverer($fixtureDir('pestconfig-matrix'));

    $first = $instance->discoverPestFiles();
    $second = $instance->discoverPestFiles();

    expect($second)->toBe($first)
        ->and($first)->not->toBeEmpty()
        ->and($first)->toBe(array_values(array_unique($first)));
});

test('every discovered file is itself recognised as a configuration file', function () use ($fixtureDir, $discoverer): void {
    $instance = $discoverer($fixtureDir('pestconfig-matrix'));

    foreach ($instance->discoverPestFiles() as $pestFile) {
        expect($instance->isPestConfigFile($pestFile))->toBeTrue()
            ->and(basename($pestFile))->toBe('Pest.php');
    }
});
