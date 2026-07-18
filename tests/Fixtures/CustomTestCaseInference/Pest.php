<?php

declare(strict_types=1);

use Tests\Type\Fixtures\CustomTestCase;
use Tests\Type\Fixtures\HelperTrait;
use Tests\Type\Fixtures\Post;

pest()->extend(CustomTestCase::class)->in('Feature');

pest()->extend(CustomTestCase::class)
    ->use(HelperTrait::class)
    ->beforeEach(function (): void {
        $this->sharedPost = new Post;
    })
    ->in('WithTrait');
