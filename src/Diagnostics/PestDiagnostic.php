<?php

declare(strict_types=1);

namespace Pest\PHPStan\Diagnostics;

final readonly class PestDiagnostic
{
    public function __construct(
        public string $identifier,
        public string $message,
        public ?string $tip = null,
        public ?int $line = null,
    ) {}
}
