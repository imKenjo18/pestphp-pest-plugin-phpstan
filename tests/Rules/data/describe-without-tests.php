<?php

declare(strict_types=1);

describe('empty group', function (): void { //
});

describe('hooks only', function (): void {
    beforeEach(function (): void {
        //
    });
});

describe('hooks with chain only', function (): void {
    beforeEach(function (): void {
        //
    })->skip();
});

describe('valid group', function (): void {
    it('has a test', function (): void {
        expect(true)->toBeTrue();
    });
});

describe('nested group', function (): void {
    describe('inner', function (): void {
        it('inner test', function (): void {
            expect(true)->toBeTrue();
        });
    });
});

describe('test something', function () {
    it('throws no exceptions', function () {
        $result = 1 + 1;
    })->throwsNoExceptions();
});

describe('todo test', function (): void {
    todo('todo');
});
