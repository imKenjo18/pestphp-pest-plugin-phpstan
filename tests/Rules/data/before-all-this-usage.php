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

beforeAll(function (): void {
    if ($this->shouldRun) {
        var_dump($this->config);
    }
});

beforeAll(function (): void {
    $callback = function (): void {
        $this->connection = new stdClass;
    };
});
