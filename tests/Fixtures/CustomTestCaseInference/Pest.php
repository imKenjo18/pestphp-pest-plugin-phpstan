<?php

declare(strict_types=1);

use Tests\Type\Fixtures\CustomTestCase;
use Tests\Type\Fixtures\HelperTrait;

pest()->extend(CustomTestCase::class)->in('Feature');

pest()->extend(CustomTestCase::class)->use(HelperTrait::class)->in('WithTrait');
