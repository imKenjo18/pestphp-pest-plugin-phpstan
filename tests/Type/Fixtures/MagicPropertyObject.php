<?php

declare(strict_types=1);

namespace Tests\Type\Fixtures;

final class MagicPropertyObject
{
    public function __get(string $name): mixed
    {
        return null;
    }
}
