<?php

declare(strict_types=1);

beforeAll(function (): void {
    $this->value = 1;
});

beforeAll(function (): void {
    expect($this->value)->toBe(1);
});

beforeAll(function (): void {
    $this->first();
    $this->second();
});

beforeAll(function (): void {
    if (true) {
        $this->nested();
    }
});

beforeAll(function (): void {
    foreach ([1, 2] as $item) {
        $this->inLoop($item);
    }
});

beforeAll(function (): void {
    $closure = function (): void {
        $this->inNestedClosure();
    };

    $closure();
});

afterAll(function (): void {
    $this->cleanup();
});

afterAll(function (): void {
    $this->a();
    $this->b();
});

beforeAll(function (): void {
    $value = 1;
    expect($value)->toBe(1);
});

afterAll(function (): void {
    $value = 1;
    expect($value)->toBe(1);
});

beforeEach(function (): void {
    $this->value = 1;
});

afterEach(function (): void {
    $this->cleanup();
});

it('may use this freely', function (): void {
    $this->assertTrue(true);
});
