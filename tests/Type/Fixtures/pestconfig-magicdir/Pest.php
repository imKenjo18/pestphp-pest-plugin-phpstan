<?php

declare(strict_types=1);

use Tests\Type\Fixtures\CustomTestCase;

pest()->extend(CustomTestCase::class)->in(__DIR__.'/Feature');
