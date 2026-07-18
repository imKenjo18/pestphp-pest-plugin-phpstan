<?php

declare(strict_types=1);

use Tests\Type\Fixtures\CustomTestCase;
use Tests\Type\Fixtures\HelperTrait;

pest()->extend(CustomTestCase::class)->in('Feat*');

pest()->use(HelperTrait::class)->in('Feature/ExampleTest.php');
