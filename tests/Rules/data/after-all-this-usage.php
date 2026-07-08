<?php

declare(strict_types=1);

afterAll(function (): void {
    $this->user = new stdClass;
});

afterAll(function (): void {
    $this->tearDownConnection();
});

afterEach(function (): void {
    $this->user = new stdClass;
});

afterAll(function (): void {
    $db = new stdClass;
});
