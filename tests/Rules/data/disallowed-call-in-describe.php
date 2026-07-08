<?php

declare(strict_types=1);

describe('group with beforeAll', function (): void {
    beforeAll(function (): void { //
    });

    it('test inside describe', function (): void {
        expect(true)->toBeTrue();
    });
});

describe('group with afterAll', function (): void {
    afterAll(function (): void { //
    });

    it('test inside describe', function (): void {
        expect(true)->toBeTrue();
    });
});

describe('group with both', function (): void {
    beforeAll(function (): void { //
    });
    afterAll(function (): void { //
    });

    it('test inside describe', function (): void {
        expect(true)->toBeTrue();
    });
});

beforeAll(function (): void {
    //
});

afterAll(function (): void {
    //
});

describe('group with hooks', function (): void {
    beforeEach(function (): void {
        //
    });

    afterEach(function (): void {
        //
    });

    it('test', function (): void {
        expect(true)->toBeTrue();
    });
});
