<?php

declare(strict_types=1);

describe('completely empty', function (): void {});

describe('hooks only', function (): void {
    beforeEach(function (): void {});
    afterEach(function (): void {});
});

describe('statements but no tests', function (): void {
    $value = 1;
    expect($value)->toBe(1);
});

describe('nested empty describes', function (): void {
    describe('inner empty', function (): void {});
});

describe('with beforeAll', function (): void {
    beforeAll(function (): void {});

    it('works', function (): void {
        expect(true)->toBeTrue();
    });
});

describe('with afterAll', function (): void {
    afterAll(function (): void {});

    it('works', function (): void {
        expect(true)->toBeTrue();
    });
});

describe('with both', function (): void {
    beforeAll(function (): void {});
    afterAll(function (): void {});

    it('works', function (): void {
        expect(true)->toBeTrue();
    });
});

describe('with nested disallowed', function (): void {
    describe('inner', function (): void {
        beforeAll(function (): void {});

        it('works', function (): void {
            expect(true)->toBeTrue();
        });
    });
});

describe('disallowed inside a conditional', function (): void {
    if (true) {
        beforeAll(function (): void {});
    }

    it('works', function (): void {
        expect(true)->toBeTrue();
    });
});

describe('with a test', function (): void {
    it('works', function (): void {
        expect(true)->toBeTrue();
    });
});

describe('with a test function', function (): void {
    test('works', function (): void {
        expect(true)->toBeTrue();
    });
});

describe('with hooks and a test', function (): void {
    beforeEach(function (): void {});
    afterEach(function (): void {});

    it('works', function (): void {
        expect(true)->toBeTrue();
    });
});

describe('with a nested describe containing a test', function (): void {
    describe('inner', function (): void {
        it('works', function (): void {
            expect(true)->toBeTrue();
        });
    });
});

describe('with tests generated in a loop', function (): void {
    foreach ([1, 2, 3] as $value) {
        it('works '.$value, function () use ($value): void {
            expect($value)->toBeInt();
        });
    }
});

describe('with a conditionally generated test', function (): void {
    if (PHP_VERSION_ID > 80000) {
        it('works', function (): void {
            expect(true)->toBeTrue();
        });
    }
});
