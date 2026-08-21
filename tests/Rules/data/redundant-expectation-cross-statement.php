<?php

declare(strict_types=1);

test('cross statement redundancy', function (): void {
    $value = random_int(0, 1) === 1 ? 1 : 'a';
    expect($value)->toBeInt();
    expect($value)->toBeInt();
});
