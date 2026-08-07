<?php

declare(strict_types=1);

namespace TestWithClosures;

use PHPUnit\Framework\TestCase;

use function PHPStan\Testing\assertType;

function testThisTypeInsideWithClosure(): void
{
    test('has correct $this type inside with closure', function (): void {
        assertType(TestCase::class, $this);
    })->with(function (): array {
        assertType(TestCase::class, $this);

        return [
            'data 1' => fn (): array => [$this],
        ];
    });
}

function testThisTypeInsideWithArray(): void
{
    test('has correct $this type inside with array', function (): void {
        assertType(TestCase::class, $this);
    })->with([
        'data 1' => fn (): array => [$this],
    ]);
}

function testThisTypeInsideNestedWithClosure(): void
{
    it('has correct $this type in nested with closures', function (): void {
        assertType(TestCase::class, $this);
    })->with([
        'data 1' => fn (): array => [
            'nested' => fn (): array => [$this],
        ],
    ]);
}

function testThisTypeInsideWithArrayOfClosures(): void
{
    test('has correct $this type in array of closures', function (): void {
        assertType(TestCase::class, $this);
    })->with([
        'data 1' => fn (): array => [$this],
        'data 2' => fn (): array => [$this],
    ]);
}

function testThisTypeInsideWithFunctionReturningNestedClosures(): void
{
    test('has correct $this type in with closure returning nested closures', function (): void {
        assertType(TestCase::class, $this);
    })->with(function (): array {
        return [
            'data 1' => fn (): array => [
                'deep' => fn (): array => [$this],
            ],
        ];
    });
}
