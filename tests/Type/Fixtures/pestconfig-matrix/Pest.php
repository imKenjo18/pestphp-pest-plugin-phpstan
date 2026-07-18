<?php

declare(strict_types=1);

use Tests\Type\Fixtures\AnotherTestCase;
use Tests\Type\Fixtures\CustomTestCase;
use Tests\Type\Fixtures\HelperTrait;

pest()->extend(CustomTestCase::class)->in('Feature');

pest()->extend(AnotherTestCase::class)->in('Unit', 'Browser');

pest()->use(HelperTrait::class)->in('Single/OnlyThis.php');

pest()->extend(CustomTestCase::class)->in('Api/*');

uses(HelperTrait::class)->in('Globbed/*Test.php');
