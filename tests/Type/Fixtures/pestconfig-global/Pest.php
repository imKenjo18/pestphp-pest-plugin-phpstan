<?php

declare(strict_types=1);

use Tests\Type\Fixtures\CustomTestCase;
use Tests\Type\Fixtures\HelperTrait;

pest()->extend(CustomTestCase::class);
pest()->use(HelperTrait::class);
