<?php

declare(strict_types=1);

namespace Tests\Type\Fixtures;

use PHPUnit\Framework\TestCase;

final class HookTestCase extends TestCase
{
    public function freezeTime(): string
    {
        return 'frozen';
    }
}
