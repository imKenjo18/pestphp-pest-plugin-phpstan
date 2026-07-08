<?php

declare(strict_types=1);

it('throws non-throwable', function (): void {
    throw new RuntimeException('error');
})->throws(stdClass::class);

it('throws exception', function (): void {
    throw new RuntimeException('error');
})->throws(RuntimeException::class);

it('throws base exception', function (): void {
    throw new Exception('error');
})->throws(Exception::class);

it('throws anything', function (): void {
    throw new RuntimeException('error');
})->throws();
