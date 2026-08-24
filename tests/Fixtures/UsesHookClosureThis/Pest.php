<?php

declare(strict_types=1);

use Tests\Type\Fixtures\HelperTrait;
use Tests\Type\Fixtures\HookTestCase;

use function PHPStan\Testing\assertType;

pest()->extend(HookTestCase::class)
    ->beforeEach(function (): void {
        assertType(HookTestCase::class, $this);
        assertType('string', $this->freezeTime());
    })
    ->in('Feature');

pest()->extend(HookTestCase::class)
    ->in('Unit')
    ->afterEach(function (): void {
        assertType(HookTestCase::class, $this);
        assertType('string', $this->freezeTime());
    });

uses(HookTestCase::class)
    ->beforeEach(function (): void {
        assertType(HookTestCase::class, $this);
        assertType('string', $this->freezeTime());
    })
    ->in('Uses');

pest()->extend(HookTestCase::class, HelperTrait::class)
    ->beforeEach(function (): void {
        assertType(HookTestCase::class, $this);
        assertType('string', $this->freezeTime());
        assertType('string', $this->helperMethod());
    })
    ->in('WithTrait');
