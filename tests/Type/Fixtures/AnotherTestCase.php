<?php

declare(strict_types=1);

namespace Tests\Type\Fixtures;

use PHPUnit\Framework\TestCase;

final class AnotherTestCase extends TestCase
{
    public function anotherHelper(): string
    {
        return 'helper';
    }
}
