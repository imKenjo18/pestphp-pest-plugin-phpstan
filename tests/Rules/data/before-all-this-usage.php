<?php

declare(strict_types=1);

beforeAll(function (): void {
    $this->user = new stdClass;
});

beforeAll(function (): void {
    $this->setup();
});

beforeEach(function (): void {
    $this->user = new stdClass;
});

beforeAll(function (): void {
    $db = new stdClass;
});
