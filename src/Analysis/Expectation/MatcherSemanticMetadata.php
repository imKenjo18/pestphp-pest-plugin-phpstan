<?php

declare(strict_types=1);

namespace Pest\PHPStan\Analysis\Expectation;

final readonly class MatcherSemanticMetadata
{
    public function __construct(
        public string $methodName,
        public ?string $requirement,
        public ?string $assertion,
    ) {}
}
