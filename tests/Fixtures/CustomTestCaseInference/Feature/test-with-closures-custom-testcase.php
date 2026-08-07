<?php

declare(strict_types=1);

namespace TestWithClosuresCustomTestCase;

use Tests\Type\Fixtures\CustomTestCase;

use function PHPStan\Testing\assertType;

function testThisTypeInsideWithClosure(): void
{
    test('has custom $this type inside with closure', function (): void {
        assertType(CustomTestCase::class, $this);
    })->with(function (): array {
        assertType(CustomTestCase::class, $this);

        return [
            'data 1' => fn (): array => [$this],
        ];
    });
}

function testThisTypeInsideWithArray(): void
{
    it('has custom $this type inside with array', function (): void {
        assertType(CustomTestCase::class, $this);
    })->with([
        'data 1' => fn (): array => [$this],
    ]);
}

function testThisTypeInsideNestedWithClosure(): void
{
    test('has custom $this type in nested with closures', function (): void {
        assertType(CustomTestCase::class, $this);
    })->with([
        'data 1' => fn (): array => [
            'nested' => fn (): array => [$this],
        ],
    ]);
}
